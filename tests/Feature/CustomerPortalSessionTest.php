<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Customers\Entities\Customer;
use Tests\TestCase;

class CustomerPortalSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_portal_uses_session_without_exposing_access_token()
    {
        $phone = '8000000000';
        $customer = Customer::create(['first_name' => 'Demo', 'email' => 'portal@example.test', 'phone_number' => $phone, 'is_active' => 1]);
        $this->getJson('/customer-portal/api/profile')->assertUnauthorized();
        $challenge = $this->postJson('/customer-portal/api/otp/request', ['phone_number' => $phone])->assertOk()->json('data.challenge_id');
        $proof = $this->postJson('/customer-portal/api/otp/verify', ['challenge_id' => $challenge, 'code' => '123456'])->assertOk()->json('data.verification_token');
        $this->postJson('/customer-portal/api/login', ['phone_number' => $phone, 'verification_token' => $proof])
            ->assertOk()->assertJsonPath('data.access_token', null)->assertSessionHas('customer_portal_id', $customer->id)->assertHeader('X-CSRF-TOKEN');
        $this->assertGuest();
        $this->assertSame(0, $customer->tokens()->count());
        $this->getJson('/customer-portal/api/profile')->assertOk()->assertJsonPath('data.id', $customer->id);
        $this->postJson('/customer-portal/api/logout')->assertOk()->assertSessionMissing('customer_portal_id');
        $this->getJson('/customer-portal/api/profile')->assertUnauthorized();
    }

    public function test_portal_is_not_exposed_in_production()
    {
        $this->app['env'] = 'production';
        $this->get('/customer-portal')->assertNotFound();
    }
}
