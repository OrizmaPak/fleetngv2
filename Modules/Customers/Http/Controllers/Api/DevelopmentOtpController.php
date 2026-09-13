<?php

namespace Modules\Customers\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class DevelopmentOtpController extends Controller
{
    private static function testOtpEnabled(Request $request): bool
    {
        if (app()->environment(['local', 'testing']) && in_array($request->ip(), ['127.0.0.1', '::1'], true)) {
            return true;
        }

        return filter_var(env('CUSTOMER_PORTAL_TEST_OTP', false), FILTER_VALIDATE_BOOLEAN);
    }

    private function developmentOnly(Request $request)
    {
        abort_unless(self::testOtpEnabled($request), 503, 'Customer verification is not configured for this environment.');
    }

    public function requestCode(Request $request)
    {
        $this->developmentOnly($request);
        $data = $request->validate(['phone_number' => ['required', 'regex:/^\d{9,13}$/']]);
        $id = Str::random(48);
        Cache::put('customer-otp:' . $id, ['phone_number' => $data['phone_number'], 'attempts' => 0], now()->addMinutes(5));
        return response()->success('Development code ready. No SMS was sent.', ['challenge_id' => $id, 'expires_in' => 300, 'development_otp' => '123456']);
    }

    public function verifyCode(Request $request)
    {
        $this->developmentOnly($request);
        $data = $request->validate(['challenge_id' => 'required|string|size:48', 'code' => 'required|string|size:6']);
        $key = 'customer-otp:' . $data['challenge_id'];
        $challenge = Cache::get($key);
        if (!$challenge || $challenge['attempts'] >= 5) return response()->badRequest('This code has expired. Request another code.');
        if (!hash_equals('123456', $data['code'])) {
            $challenge['attempts']++;
            Cache::put($key, $challenge, now()->addMinutes(5));
            return response()->badRequest('Incorrect code. The development code is 123456.');
        }
        Cache::forget($key);
        $proof = Str::random(64);
        Cache::put('customer-verified:' . hash('sha256', $proof), $challenge['phone_number'], now()->addMinutes(5));
        return response()->success('Development phone verification complete.', ['verification_token' => $proof]);
    }

    public static function consumeProof(Request $request)
    {
        if (!self::testOtpEnabled($request)) return false;
        $proof = $request->input('verification_token');
        if (!is_string($proof) || strlen($proof) !== 64) return false;
        $phone = Cache::pull('customer-verified:' . hash('sha256', $proof));
        return is_string($phone) && hash_equals($phone, (string) $request->input('phone_number'));
    }
}
