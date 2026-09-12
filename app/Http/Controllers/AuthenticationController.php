<?php

namespace App\Http\Controllers;

use App\Models\ApiModel;
use App\Models\FrontModel;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;

class AuthenticationController extends Controller
{

  private $api_url;
  public function __construct()
  {
    $this->api_url = rtrim(config('app.url'), '/') . '/api/';
  }

  public function superadmin_login(Request $request)
  {
    $pageConfigs = ['blankPage' => true]; //for design

    if ($request->isMethod('post')) {
      $email    = $request->email;
      $password = $request->password;
      $user_type = 1;
      $params = array('email' => $email, 'password' => $password, 'user_type' => $user_type);
      $login_url  = $this->api_url . "superadmin-login";

      $user_login = FrontModel::callPostCurl($login_url, $params);
      if ($user_login['success']) {
        Auth::loginUsingId($user_login['user_details']['id']);
        Session::put('user_role', auth()->user()->user_type);

        if ($request->remember_me == 'remember') {
          $data = [
            'email' => $email,
            'pswd' => $password,
          ];

          setcookie('user_cookies', serialize($data), time() + (86400 * 30), "/");
        } else {
          setcookie("user_cookies", " ", time() - 3600, '/');
        }

        return redirect('/superadmin/analytics');
      } else {

        Session::flash('error_message', $user_login['message']);

        return view('/content/authentication/auth-superadmin-login', ['pageConfigs' => $pageConfigs]);
      }
    } else {
      return view('/content/authentication/auth-superadmin-login', ['pageConfigs' => $pageConfigs]);
    }
  }

  public function admin_login(Request $request)
  {

    $pageConfigs = ['blankPage' => true]; //for design

    if ($request->isMethod('post')) {
      $email    = $request->email;
      $password = $request->password;
      $user_type = 2;
      $params = array('email' => $email, 'password' => $password, 'user_type' => $user_type);
      $login_url              = $this->api_url . "admin-login";

      $user_login             = FrontModel::callPostCurl($login_url, $params);
      if ($user_login['success']) {
        Auth::loginUsingId($user_login['user_details']['id']);
        Session::put('user_role', auth()->user()->user_type);

        if ($request->remember_me == 'remember') {
          $data = [
            'email' => $email,
            'pswd' => $password,
          ];

          setcookie('user_cookies', serialize($data), time() + (86400 * 30), "/");
        } else {
          setcookie("user_cookies", " ", time() - 3600, '/');
        }

        return redirect('/analytics');
      } else {

        Session::flash('error_message', $user_login['message']);

        return view('/content/authentication/auth-admin-login', ['pageConfigs' => $pageConfigs]);
      }
    } else {
      return view('/content/authentication/auth-admin-login', ['pageConfigs' => $pageConfigs]);
    }
  }


  public function user_login(Request $request)
  {

    $pageConfigs = ['blankPage' => true]; //for design

    if ($request->isMethod('post')) {
      $email    = $request->email;
      $password = $request->password;
      $user_type = 3;
      $params = array('email' => $email, 'password' => $password, 'user_type' => $user_type);
      $login_url              = $this->api_url . "admin-login";

      $user_login             = FrontModel::callPostCurl($login_url, $params);

      if ($user_login['success']) {
        Auth::loginUsingId($user_login['user_details']['id']);
        Session::put('user_role', auth()->user()->user_type);

        if ($request->remember_me == 'remember') {
          $data = [
            'email' => $email,
            'pswd' => $password,
          ];

          setcookie('user_cookies', serialize($data), time() + (86400 * 30), "/");
        } else {
          setcookie("user_cookies", " ", time() - 3600, '/');
        }

        return redirect('/analytics');
      }

      Session::flash('error_message', $user_login['message']);
      return back()->withInput();
    }

    return view('/content/authentication/auth-user-login', ['pageConfigs' => $pageConfigs]);
  }

  public function payment_user_login(Request $request)
  {

    $pageConfigs = ['blankPage' => true]; //for design

    if ($request->isMethod('post')) {
      $email    = $request->email;
      $password = $request->password;
      $user_type = 5;
      $params = array('email' => $email, 'password' => $password, 'user_type' => $user_type);
      $login_url              = $this->api_url . "admin-login";

      $user_login             = FrontModel::callPostCurl($login_url, $params);

      if ($user_login['success']) {
        Auth::loginUsingId($user_login['user_details']['id']);
        Session::put('user_role', auth()->user()->user_type);

        if ($request->remember_me == 'remember') {
          $data = [
            'email' => $email,
            'pswd' => $password,
          ];

          setcookie('user_cookies', serialize($data), time() + (86400 * 30), "/");
        } else {
          setcookie("user_cookies", " ", time() - 3600, '/');
        }

        return redirect('/analytics');
      } else {

        Session::flash('error_message', $user_login['message']);

        return view('/content/authentication/auth-payment-user-login', ['pageConfigs' => $pageConfigs]);
      }
    } else {
      return view('/content/authentication/auth-payment-user-login', ['pageConfigs' => $pageConfigs]);
    }
  }

  public function logout(Request $request)
  {
    $role = session('user_role');
    User::where('id', Auth::id())->update(['last_active' => Carbon::now()]);
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    if ($role == 1) {
      return redirect('/superadmin');
    } elseif ($role == 2) {
      return redirect('/admin');
    } else {
      return redirect('/user');
    }
  }

  // Forgot Password v2
  public function forgot_password(Request $request)
  {
    $pageConfigs = ['blankPage' => true];

    if ($request->isMethod('post')) {

      $email    = $request->email;

      $params = array('email' => $email);
      $url              = $this->api_url . "forgot-password";
      $forget_password   = FrontModel::callPostCurl($url, $params);

      if ($forget_password['success']) {
        return redirect()->back()->with('success_message', $forget_password['message']);
      } else {
        return redirect()->back()->with('error_message', $forget_password['message']);
      }
    } else {
      return view('/content/authentication/auth-forgot-password', ['pageConfigs' => $pageConfigs]);
    }
  }

  public function reset_password(Request $request, $title = null)
  {
    $user = is_string($title) && strlen($title) === 64 ? \App\Models\User::where('token', $title)->first() : null;
    $reset = $user ? \Illuminate\Support\Facades\DB::table('password_resets')->where('email', $user->email)->first() : null;
    if (!$user || !$reset || !hash_equals($reset->token, hash('sha256', $title)) || \Carbon\Carbon::parse($reset->created_at)->addHour()->isPast()) {
      return redirect('forgot-password')->with('error_message', 'This reset link has expired or is invalid. Request another link.');
    }
    if (!$request->isMethod('post')) {
      return view('content.authentication.auth-reset-password', ['dbresponse'=>['token'=>$title], 'pageConfigs'=>['blankPage'=>true], 'type'=>$user->user_type]);
    }
    $request->validate(['password'=>'required|string|min:8|max:128|confirmed']);
    \Illuminate\Support\Facades\DB::transaction(function () use ($user, $title, $request) {
      $locked = \App\Models\User::whereKey($user->id)->lockForUpdate()->firstOrFail();
      abort_unless(hash_equals((string)$locked->token, $title), 422, 'This reset link has already been used.');
      $locked->forceFill(['password'=>bcrypt($request->password), 'token'=>null, 'remember_token'=>\Illuminate\Support\Str::random(60)])->save();
      \Illuminate\Support\Facades\DB::table('password_resets')->where('email',$user->email)->delete();
    });
    $data = ['user_name'=>$user->first_name,'email'=>$user->email,'today_date'=>now()->format('M d,Y'),'subject'=>'Password has been changed'];
    try {
      Mail::send('mails.thankyou-reset',$data,function ($message) use ($user) { $message->to($user->email)->subject('Password changed successfully'); });
    } catch (\Throwable $error) { report($error); }
    $destination = (int)$user->user_type === 1 ? '/superadmin' : ((int)$user->user_type === 2 ? '/admin' : '/user');
    return redirect($destination)->with('success_message','Password changed successfully.');
  }
}
