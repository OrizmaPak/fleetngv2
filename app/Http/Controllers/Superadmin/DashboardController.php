<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\User;

class DashboardController extends Controller
{
  // Dashboard - Analytics
  public function dashboardAnalytics()
  {
    $pageConfigs = ['pageHeader' => false];

    $total_users = User::where('user_type', 3)->count();
    $total_active_users = User::where('user_type', 3)->where('is_active', 1)->count();
    $total_inactive_users = User::where('user_type', 3)->where('is_active', 0)->count();

    $total_merchants = User::where('user_type', 4)->count();
    $total_active_merchants = User::where('user_type', 4)->where('is_active',1)->count();
    $total_inactive_merchants = User::where('user_type', 4)->where('is_active',0)->count();
    
    return view('/content/dashboard/superadmin-dashboard-analytics', ['pageConfigs' => $pageConfigs,'total_users'=>$total_users, 'total_active_users'=>$total_active_users ,'total_inactive_users'=>$total_inactive_users ,'total_merchants'=>$total_merchants,'total_active_merchants'=>$total_active_merchants,'total_inactive_merchants'=>$total_inactive_merchants]);
  }

}
