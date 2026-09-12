<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Customers\Entities\Customer;
use Modules\Customers\Entities\Trip;
use Modules\Customers\Entities\DraftTrip;
use Tests\TestCase;

class CustomerAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_require_identity_and_ownership_and_running_trips_cannot_be_reassigned()
    {
        $one = $this->customer(1);
        $two = $this->customer(2);
        $driver = \App\Models\Driver::create(['first_name'=>'Active','is_active'=>1]);
        $trip = Trip::create(['client_id'=>$one->id,'driver_id'=>$driver->id,'status'=>2,'total_cost'=>100]);
        $this->postJson('/api/trips/'.$trip->id.'/send-notification')->assertUnauthorized();
        Sanctum::actingAs($two);
        $this->postJson('/api/trips/'.$trip->id.'/send-notification')->assertNotFound();
        Sanctum::actingAs($one);
        $this->patchJson('/api/customer/trips/'.$trip->id.'/driver',['driver'=>$driver->id])->assertStatus(422);
    }

    private function customer($number)
    {
        return Customer::create(['first_name' => 'Customer', 'email' => 'customer' . $number . '@example.test', 'phone_number' => '800000000' . $number, 'is_active' => 1]);
    }

    public function test_customer_reads_and_mutations_reject_foreign_records()
    {
        $one = $this->customer(1);
        $two = $this->customer(2);
        $driver = \App\Models\Driver::create(['first_name' => 'Test driver']);
        $trip = Trip::create(['client_id' => $two->id, 'driver_id' => $driver->id, 'total_cost' => 100]);
        $draft = DraftTrip::create(['client_id' => $two->id, 'total_cost' => 100]);
        Sanctum::actingAs($one);
        $this->getJson('/api/customer/trips/' . $trip->id)->assertNotFound();
        $this->patchJson('/api/customer/trips/' . $trip->id . '/driver', ['driver' => 1])->assertNotFound();
        $this->getJson('/api/customer/draft/trips/' . $draft->id)->assertNotFound();
        $this->postJson('/api/customer/draft/trips/' . $draft->id, [])->assertNotFound();
        $this->postJson('/api/customer/draft/trips/' . $draft->id . '/confirm')->assertNotFound();
        $this->postJson('/api/customer/trips/payment-link', ['trip_ids' => [$trip->id]])->assertNotFound();
    }

    public function test_inactive_and_staff_identities_cannot_use_customer_portal()
    {
        $customer = $this->customer(1);
        $customer->update(['is_active' => 0]);
        Sanctum::actingAs($customer);
        $this->getJson('/api/customer/profile')->assertForbidden();
        $staff = User::create(['first_name' => 'Staff', 'email' => 'staff@example.test', 'user_type' => 1]);
        $this->actingAs($staff, 'sanctum')->getJson('/api/customer/profile')->assertForbidden();
    }

    public function test_profile_hides_authentication_fields()
    {
        $customer = $this->customer(1);
        $customer->update(['password' => 'private-value', 'remember_token' => 'private-token']);
        Sanctum::actingAs($customer);
        $response = $this->getJson('/api/customer/profile')->assertOk();
        $this->assertArrayNotHasKey('password', $response->json('data'));
        $this->assertArrayNotHasKey('remember_token', $response->json('data'));
    }
}
