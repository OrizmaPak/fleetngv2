<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Sanctum\Sanctum;
use Modules\Customers\Entities\Customer;
use Modules\Customers\Entities\Checkout;
use Modules\Customers\Entities\Trip;
use Modules\Customers\Entities\TripPayment;
use Modules\Customers\Services\CheckoutService;
use Tests\TestCase;

class CustomerCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function fixture()
    {
        config(['customers.payments_enabled'=>true,'app.flutterwave_secret_key'=>'test-only']);
        $customer = Customer::create(['first_name'=>'Checkout','email'=>'checkout@example.test','phone_number'=>'8000000000','is_active'=>1]);
        Sanctum::actingAs($customer);
        $driver = \App\Models\Driver::create(['first_name'=>'Driver']);
        return Trip::create(['driver_id'=>$driver->id,'client_id'=>$customer->id,'total_cost'=>100,'road_money'=>20,'cost_of_sand'=>30]);
    }

    private function start($trip)
    {
        Http::fake(['*/payments'=>Http::response(['status'=>'success','data'=>['link'=>'https://checkout.flutterwave.com/pay/test']])]);
        $this->postJson('/api/customer/trips/payment-link',['trip_ids'=>[$trip->id]])->assertOk();
        return Checkout::firstOrFail();
    }

    public function test_verified_callback_is_idempotent_and_includes_all_costs()
    {
        $trip = $this->fixture();
        $checkout = $this->start($trip);
        $this->assertSame(15000,$checkout->amount_minor);
        Http::fake(['*/verify'=>Http::response(['status'=>'success','data'=>['id'=>123,'tx_ref'=>$checkout->reference,'status'=>'successful','amount'=>150,'currency'=>'NGN','payment_type'=>'card']])]);
        $service = app(CheckoutService::class);
        $this->assertSame('successful',$service->verify('123')->status);
        $this->assertSame('successful',$service->verify('123')->status);
        $this->assertSame(1,TripPayment::count());
        $this->assertNotNull($trip->fresh()->payment_id);
    }

    public function test_incorrect_verification_details_never_mark_paid()
    {
        $trip = $this->fixture();
        $checkout = $this->start($trip);
        $base = ['id'=>123,'tx_ref'=>$checkout->reference,'status'=>'successful','amount'=>150,'currency'=>'NGN'];
        foreach ([['amount'=>100],['amount'=>151],['currency'=>'USD'],['tx_ref'=>'forged'],['id'=>456]] as $change) {
            Http::fake(['*/verify'=>Http::response(['status'=>'success','data'=>array_merge($base,$change)])]);
            $this->assertNull(app(CheckoutService::class)->verify('123'));
            $this->assertSame(0,TripPayment::count());
        }
        $trip->update(['total_cost'=>200]);
        Http::fake(['*/verify'=>Http::response(['status'=>'success','data'=>$base])]);
        $this->assertNull(app(CheckoutService::class)->verify('123'));
    }

    public function test_unavailable_gateway_and_ineligible_selection_fail_without_payment()
    {
        $trip = $this->fixture();
        Http::fake();
        config(['customers.payments_enabled'=>false]);
        $this->postJson('/api/customer/trips/payment-link',['trip_ids'=>[$trip->id]])->assertStatus(503);
        config(['customers.payments_enabled'=>true]);
        $this->postJson('/api/customer/trips/payment-link',['trip_ids'=>[]])->assertStatus(422);
        $trip->update(['status'=>4]);
        $this->postJson('/api/customer/trips/payment-link',['trip_ids'=>[$trip->id]])->assertStatus(422);
        Http::assertNothingSent();
        $this->assertSame(0,Checkout::count());
    }
}
