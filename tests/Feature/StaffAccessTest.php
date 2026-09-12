<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FrontModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_vehicle_filter_cannot_be_used_as_a_foreign_driver_id()
    {
        $user = User::create(['first_name'=>'Company','email'=>'report-own@example.test','user_type'=>3,'is_active'=>1]);
        $other = User::create(['first_name'=>'Other','email'=>'report-other@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Foreign','user_id'=>$other->id,'vehicle_id'=>'OTHER','is_active'=>1]);
        Trip::create(['driver_id'=>$driver->id,'client_name'=>'Private client','total_cost'=>100,'trip_generated_at'=>now()]);
        $this->actingAs($user);
        $report = \App\Models\ApiModel::getTripReport((string)$driver->id,null,null);
        $this->assertEmpty($report['data']);
    }

    public function test_unknown_and_foreign_tracking_devices_do_not_fall_back_to_first_vehicle()
    {
        config(['integrations.tracking_enabled'=>true,'integrations.tracking_username'=>'test','integrations.tracking_key'=>'test']);
        $user = User::create(['first_name'=>'Company','email'=>'tracking@example.test','user_type'=>3,'is_active'=>1]);
        Driver::create(['first_name'=>'Own','user_id'=>$user->id,'is_active'=>1,'device_serial_number'=>'own']);
        \Illuminate\Support\Facades\Http::fake(['app.zypsa.com/*'=>\Illuminate\Support\Facades\Http::response(['data'=>[
            'vehicles'=>[['device_id'=>10,'serial'=>'foreign'],['device_id'=>20,'serial'=>'own']],
            'vehicle_data'=>[],
        ]])]);
        $this->actingAs($user)->get('/map-tracking/10')->assertNotFound();
        $this->get('/map-tracking/999')->assertNotFound();
        \Illuminate\Support\Facades\Http::assertSentCount(4);
    }

    public function test_anonymous_admin_api_and_invoice_are_protected()
    {
        $this->postJson('/api/get-user-detail',['user_id'=>1])->assertUnauthorized();
        $this->postJson('/api/delete-driver',['driver_id'=>1])->assertUnauthorized();
        $this->getJson('/trip/1/invoice/pdf-download')->assertUnauthorized();
    }

    public function test_company_cannot_read_foreign_driver_or_enter_superadmin()
    {
        $user = User::create(['first_name'=>'Company','email'=>'company@example.test','user_type'=>3,'is_active'=>1]);
        $other = User::create(['first_name'=>'Other','email'=>'other@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Foreign','user_id'=>$other->id]);
        $trip = Trip::create(['driver_id'=>$driver->id,'total_cost'=>100]);
        $this->actingAs($user)->get('/driver/view/'.$driver->id)->assertNotFound();
        $this->get('/trip/'.$trip->id.'/invoice/pdf-download')->assertNotFound();
        $this->get('/superadmin/merchant-list')->assertForbidden();
        $this->post('/driver/delete',['driver_id'=>$driver->id])->assertNotFound();
        $this->assertDatabaseHas('drivers',['id'=>$driver->id]);
    }

    public function test_internal_api_uses_same_contract_without_network()
    {
        $user = User::create(['first_name'=>'Company','email'=>'company@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Own','user_id'=>$user->id]);
        $this->actingAs($user);
        config(['app.url'=>'http://unreachable.invalid/']);
        $response = FrontModel::callPostCurl('http://unreachable.invalid/api/get-driver-detail',['driver_id'=>$driver->id]);
        $this->assertTrue($response['success']);
        $this->assertEquals($driver->id,$response['data']['id']);
    }
}
