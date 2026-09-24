<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class SessionLoginController extends Controller
{
    public function admin(Request $request) { return $this->login($request, [2], 'auth-admin-login', '/analytics'); }
    public function user(Request $request) { return $this->login($request, [1, 2, 3, 5], 'auth-user-login', '/analytics'); }
    public function superadmin(Request $request) { return $this->login($request, [1], 'auth-superadmin-login', '/superadmin/analytics'); }

    private function login(Request $request, array $roles, string $view, ?string $destination)
    {
        if (!$request->isMethod('post')) return view('content.authentication.' . $view, ['pageConfigs' => ['blankPage' => true]]);
        $data = $request->validate(['email' => 'required|email|max:255', 'password' => 'required|string|max:4096']);
        $key = 'staff-login:' . hash('sha256', strtolower($data['email']) . '|' . $request->ip());
        if (RateLimiter::tooManyAttempts($key, 10)) return back()->withInput($request->only('email'))->with('error_message', 'Too many sign-in attempts. Please try again in a minute.');
        $user = User::where('email', $data['email'])->whereIn('user_type', $roles)->first();
        if (!$user || !$user->is_active || !Hash::check($data['password'], $user->password)) {
            RateLimiter::hit($key, 60);
            return back()->withInput($request->only('email'))->with('error_message', 'Invalid email or password, or this account is unavailable.');
        }
        RateLimiter::clear($key);
        Auth::login($user, $request->input('remember_me') === 'remember');
        $request->session()->regenerate();
        $request->session()->put('user_role', $user->user_type);
        $user->update(['last_active' => now()]);
        Cookie::queue(Cookie::forget('user_cookies'));
        return redirect($user->is_payment_user || (int) $user->user_type === 5 ? '/trip-list' : ($destination ?: $this->destinationFor($user)));
    }

    private function destinationFor(User $user): string
    {
        return '/analytics';
    }
}
