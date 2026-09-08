<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\FrontModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
  private $api_url;
  public function __construct()
  {
    $this->api_url = config('app.url') . 'api/';
  }

  // Account Settings
  public function account_settings()
  {
    $breadcrumbs = [['link' => "/superadmin/analytics", 'name' => "Home"], ['name' => "Change Password"]];
    return view('/superadmin/page-account-settings', ['breadcrumbs' => $breadcrumbs]);
  }

  //function used to change password
  public function change_password(Request $request)
  {
    $url = $this->api_url . "change-password";

    $params = [
      'user_id' => Auth::id(),
      'new_password' => $request->new_password,
      'old_password' => $request->old_password
    ];

    $change_password = FrontModel::callPostCurl($url, $params);

    if ($change_password['success']) {
      return redirect()->back()->with('success_message', $change_password['message']);
    } else {
      return redirect()->back()->with('error_message', $change_password['message']);
    }
  }
}
