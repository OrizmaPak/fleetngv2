<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Modules\Customers\Entities\Customer;
use Modules\Customers\Entities\DraftTrip;
use Modules\Customers\Entities\DropLocation;
use Modules\Customers\Entities\PickupLocation;
use Modules\Customers\Entities\Trip;
use Tests\TestCase;

class CustomerDraftWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_draft_requires_driver_and_confirms_only_once()
    {
        $customer = Customer::create(['first_name'=>'Demo','email'=>'draft@example.test','phone_number'=>'8000000001','is_active'=>1]);
        Sanctum::actingAs($customer);
        $pickup = PickupLocation::create(['location'=>'Pickup','location_name'=>'Depot']);
        $payload = ['pickup_location'=>$pickup->id,'pickup_datetime'=>now()->addDay()->toDateTimeString(),'drop_off_location'=>'Site','cost'=>100];
        $id = $this->postJson('/api/customer/draft/trips/create',$payload)->assertOk()->json('data.id');
        $this->postJson('/api/customer/draft/trips/'.$id.'/confirm')->assertStatus(400);
        $this->assertSame(0,Trip::count());
        $driver = \App\Models\Driver::create(['first_name'=>'Driver']);
        $foreignDrop = DropLocation::create(['location'=>'Other customer site']);
        $payload['driver'] = $driver->id;
        $payload['drop_location_id'] = $foreignDrop->id;
        $payload['drop_off_location'] = 'Updated site';
        $this->postJson('/api/customer/draft/trips/'.$id,$payload)->assertOk();
        $this->assertSame('Other customer site',$foreignDrop->fresh()->location);
        $this->postJson('/api/customer/draft/trips/'.$id.'/confirm')->assertOk();
        $this->assertSame(1,Trip::count());
        $this->assertSame(0,DraftTrip::count());
        $this->postJson('/api/customer/draft/trips/'.$id.'/confirm')->assertNotFound();
        $this->assertSame(1,Trip::count());
    }
}
