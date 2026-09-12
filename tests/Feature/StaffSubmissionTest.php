<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Customer;
use App\Models\TripPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StaffSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_form_submission_saves_costs_and_rejects_foreign_driver()
    {
        $user = User::create(['first_name'=>'Company','email'=>'trip@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Own','user_id'=>$user->id,'is_active'=>1]);
        $other = User::create(['first_name'=>'Other','email'=>'other-trip@example.test','user_type'=>3,'is_active'=>1]);
        $foreign = Driver::create(['first_name'=>'Other','user_id'=>$other->id,'is_active'=>1]);
        $pickup = \App\Models\PickupLocation::create(['location'=>'Depot','user_id'=>$user->id,'is_active'=>1]);
        $data = ['client_type'=>'new','client_name'=>'Customer','client_phone'=>'8000000099','client_email'=>'new@example.test','country_code'=>'+234','driver_id'=>$driver->id,'pickup_location'=>$pickup->id,'drop_location'=>'Review delivery','pickup_datetime'=>now()->addDay()->format('Y-m-d H:i:s'),'trip_cost'=>100,'road_money'=>20,'cost_of_sand'=>30];
        $this->actingAs($user)->post('/trip/add',$data)->assertRedirect('/trip-list');
        $this->assertSame(1,Trip::count());
        $this->assertEquals(150,Trip::first()->total_trip_cost());
        $this->post('/trip/add',array_merge($data,['driver_id'=>$foreign->id]))->assertNotFound();
        $this->assertSame(1,Trip::count());
    }

    public function test_pickup_coordinates_can_be_created_and_updated_without_maps()
    {
        \Illuminate\Support\Facades\Http::fake();
        $user = User::create(['first_name'=>'Company','email'=>'coordinates@example.test','user_type'=>3,'is_active'=>1]);
        $data = ['location'=>'Review address','location_name'=>'Review depot','latitude'=>6.5,'longitude'=>3.4];
        $this->actingAs($user)->post('/pickup-location/add', $data)->assertRedirect('/location-list');
        $location = \App\Models\PickupLocation::firstOrFail();
        $this->assertEquals(6.5, $location->latitude);
        $this->post('/pickup-location/edit/'.$location->id, array_merge($data, ['latitude'=>7.1]))->assertRedirect('/location-list');
        $this->assertEquals(7.1, $location->fresh()->latitude);
        $this->postJson('/pickup-location/edit/'.$location->id, array_merge($data, ['latitude'=>91]))->assertStatus(422);
        $this->assertEquals(7.1, $location->fresh()->latitude);
        \Illuminate\Support\Facades\Http::assertNothingSent();
    }

    public function test_manual_payment_retries_and_mixed_selection_are_atomic()
    {
        $merchant = User::create(['first_name'=>'Merchant','email'=>'m@example.test','user_type'=>4,'is_active'=>1]);
        $user = User::create(['first_name'=>'Payment','email'=>'p@example.test','user_type'=>3,'is_active'=>1,'is_payment_user'=>1,'merchant_assigned'=>$merchant->id]);
        $driver = Driver::create(['first_name'=>'Driver','merchant_id'=>$merchant->id]);
        $trip = Trip::create(['driver_id'=>$driver->id,'total_cost'=>100,'road_money'=>20]);
        $canceled = Trip::create(['driver_id'=>$driver->id,'total_cost'=>100,'status'=>4]);
        $this->actingAs($user)->postJson('/trip/bulk-pos-payment-received',['pos_trip_ids'=>$trip->id.','.$canceled->id])->assertStatus(422);
        $this->assertSame(0,TripPayment::count());
        $this->post('/trip/'.$trip->id.'/pos-payment-confirm')->assertRedirect('/trip-list');
        $this->post('/trip/'.$trip->id.'/pos-payment-confirm')->assertRedirect('/trip-list');
        $this->assertSame(1,TripPayment::count());
        $this->assertEquals(120,TripPayment::first()->amount);
        $this->postJson('/trip/'.$trip->id.'/bank-payment-received')->assertStatus(422);
        $this->assertSame(1,TripPayment::count());
    }

    public function test_customer_status_get_is_read_only_and_post_is_explicit()
    {
        $user = User::create(['first_name'=>'Admin','email'=>'a@example.test','user_type'=>1,'is_active'=>1]);
        $customer = Customer::create(['first_name'=>'Client','email'=>'c@example.test','phone_number'=>'8000000001','is_active'=>1]);
        $this->actingAs($user)->get('/client-status-change/'.$customer->id)->assertOk();
        $this->assertEquals(1,$customer->fresh()->is_active);
        $this->post('/client-status-change/'.$customer->id,['status'=>0])->assertRedirect('/client-list');
        $this->assertEquals(0,$customer->fresh()->is_active);
        $this->post('/client-status-change/'.$customer->id,['status'=>0])->assertRedirect('/client-list');
        $this->assertEquals(0,$customer->fresh()->is_active);
    }

    public function test_reset_token_expiry_confirmation_and_single_use()
    {
        Mail::fake();
        $token = str_repeat('a',64);
        $user = User::create(['first_name'=>'Reset','email'=>'r@example.test','user_type'=>2,'is_active'=>1,'token'=>$token,'password'=>Hash::make('Original123!')]);
        $user->forceFill(['token'=>$token])->save();
        DB::table('password_resets')->insert(['email'=>$user->email,'token'=>hash('sha256',$token),'created_at'=>now()]);
        $this->postJson('/reset-password/'.$token,['password'=>'NewPassword123!','password_confirmation'=>'wrong'])->assertStatus(422);
        $this->post('/reset-password/'.$token,['password'=>'NewPassword123!','password_confirmation'=>'NewPassword123!'])->assertRedirect('/admin');
        $this->assertTrue(Hash::check('NewPassword123!',$user->fresh()->password));
        $this->post('/reset-password/'.$token,['password'=>'Another123!','password_confirmation'=>'Another123!'])->assertRedirect('forgot-password');
        $user->forceFill(['token'=>$token])->save();
        DB::table('password_resets')->insert(['email'=>$user->email,'token'=>hash('sha256',$token),'created_at'=>now()->subHours(2)]);
        $this->get('/reset-password/'.$token)->assertRedirect('forgot-password');
    }
}
