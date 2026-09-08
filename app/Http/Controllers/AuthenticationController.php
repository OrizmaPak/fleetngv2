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
    User::where('id', Auth::id())->update(['last_active' => Carbon::now()]);
    Auth::logout();
    if (session('user_role') == 1) {
      return redirect('/superadmin');
    } elseif (session('user_role') == 2) {
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
    $pageConfigs = ['blankPage' => true];

    $password = $request->password;
    $type = $request->type ?? '1';
    $password_confirmation = $request->password_confirmation;

    $where_data   = array('token' => $title);
    $user         = ApiModel::fetchSingleRecord('users', $where_data);


    if ($request->isMethod('post')) {
      if (!empty($user)) {
        $user_token = $user->token;
        if ($password != $password_confirmation) {
          return redirect(url('reset-password') . $title . '?signature=' . bcrypt($user->id))->with('error_message', 'Password and Confirm password should be same');
        }
        if ($user_token == $title) {

          $reset_data  = array('password' => bcrypt($password), 'token' => "");
          ApiModel::editRecord('users', $where_data, $reset_data);
          //Send mail to registered user for email verification.                  
          $name = $user->first_name;
          $email = $user->email;
          $today_date = date("M d,Y");
          $data = array('user_name' => $name, 'email' => $email, 'today_date' => $today_date, 'subject' => "Password has been changed");

          Mail::send('mails.thankyou-reset', $data, function ($message) use ($data) //Send Mail
          {
            $message->to($data['email'])->subject('Password changed successfully');
          });

          if ($request->type === '1') {
            return redirect('/superadmin')->with('success_message', 'Password changed successfully.');
          }

          return redirect(url('user'))->with('success_message', 'Password changed successfully.');
        } else {
          return redirect(url('reset-password') . $title . '?signature=' . bcrypt($user->id))->with('erro_message', 'Token does not match. Please try again with new reset link');
        }
      } else {
        return redirect('forgot-password')->with('error_message', 'Token does not match. Please try again with new reset link');
      }
    }
    $dbresponse['token'] = $title;

    return view('/content/authentication/auth-reset-password', ['dbresponse' => $dbresponse, 'pageConfigs' => $pageConfigs, 'type' => $type]);
  }
}
