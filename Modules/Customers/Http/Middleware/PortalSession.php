<?php

namespace Modules\Customers\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;
use Modules\Customers\Entities\Customer;

class PortalSession
{
    public function handle(Request $request, Closure $next)
    {
        $customer = Customer::find($request->session()->get('customer_portal_id'));
        $request->setUserResolver(function () use ($customer) { return $customer; });
        $response = $next($request);
        $path = $request->path();
        if (in_array($path, ['customer-portal/api/login', 'customer-portal/api/register'], true) && $response->isSuccessful()) {
            $payload = json_decode($response->getContent(), true);
            $plainToken = $payload['data']['access_token'] ?? null;
            if ($plainToken) {
                $token = PersonalAccessToken::findToken($plainToken);
                if ($token && $token->tokenable instanceof Customer) {
                    $request->session()->regenerate();
                    $request->session()->put('customer_portal_id', $token->tokenable_id);
                    $token->delete();
                    unset($payload['data']['access_token']);
                    $response->setData($payload);
                }
            }
        }
        if ($path === 'customer-portal/api/logout' && $response->isSuccessful()) {
            $request->session()->forget('customer_portal_id');
            $request->session()->regenerate();
        }
        $response->headers->set('X-CSRF-TOKEN', $request->session()->token());
        $response->headers->set('Cache-Control', 'no-store, private');
        return $response;
    }
}
