<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Modules\Customers\Entities\Customer;
use Tests\TestCase;

class DevelopmentCustomerOtpTest extends TestCase
{
    use RefreshDatabase;

    public function test_mock_code_proof_is_required_and_single_use()
    {
        $phone = '8000000000';
        Customer::create(['first_name' => 'Demo', 'email' => 'demo@example.test', 'phone_number' => $phone, 'is_active' => 1]);
        $this->postJson('/api/customer/login', ['phone_number' => $phone])->assertStatus(400);
        $challenge = $this->postJson('/api/customer/otp/request', ['phone_number' => $phone])->assertOk()->assertJsonPath('data.development_otp', '123456')->json('data.challenge_id');
        $this->postJson('/api/customer/otp/verify', ['challenge_id' => $challenge, 'code' => '999999'])->assertStatus(400);
        $proof = $this->postJson('/api/customer/otp/verify', ['challenge_id' => $challenge, 'code' => '123456'])->assertOk()->json('data.verification_token');
        $response = $this->postJson('/api/customer/login', ['phone_number' => $phone, 'verification_token' => $proof])->assertOk();
        $this->assertNotEmpty($response->json('data.access_token'));
        $this->postJson('/api/customer/login', ['phone_number' => $phone, 'verification_token' => $proof])->assertStatus(400);
    }

    public function test_mock_verification_is_disabled_in_production()
    {
        $this->app['env'] = 'production';
        $this->postJson('/api/customer/otp/request', ['phone_number' => '8000000000'])->assertStatus(503);
        $this->postJson('/api/customer/otp/verify', ['challenge_id' => str_repeat('a', 48), 'code' => '123456'])->assertStatus(503);
    }

    public function test_challenges_expire_and_stop_after_five_incorrect_attempts()
    {
        $challenge = $this->postJson('/api/customer/otp/request', ['phone_number' => '8000000001'])->assertOk()->json('data.challenge_id');
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/customer/otp/verify', ['challenge_id' => $challenge, 'code' => '000000'])->assertStatus(400);
        }
        $this->postJson('/api/customer/otp/verify', ['challenge_id' => $challenge, 'code' => '123456'])->assertStatus(400);
        $challenge = $this->postJson('/api/customer/otp/request', ['phone_number' => '8000000001'])->assertOk()->json('data.challenge_id');
        $this->travel(6)->minutes();
        $this->postJson('/api/customer/otp/verify', ['challenge_id' => $challenge, 'code' => '123456'])->assertStatus(400);
        $this->travelBack();
    }

    public function test_mock_verification_rejects_non_loopback_requests()
    {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.1'])
            ->postJson('/api/customer/otp/request', ['phone_number' => '8000000001'])->assertStatus(503);
    }
}
