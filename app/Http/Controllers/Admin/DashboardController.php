<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\AnalyticsTrait;
use App\Helper;

class DashboardController extends Controller
{
  use AnalyticsTrait;

  // Dashboard - Analytics
  public function dashboardAnalytics()
  {
    $users = $this->getUsersCount();
    $drivers = $this->getDriversCount();
    $trips = $this->getTripStats();
    $revenue = $this->getRevenue();
    $commissions = $this->getCommissions();
    $expenses = $this->getExpenses();
    $savings = $this->getSavingsAnalytics();

    $data = [
      'total_users' => $users['total_users'],
      'total_active_users' => $users['total_active_users'],
      'total_inactive_users' => $users['total_inactive_users'],
      'total_drivers' => $drivers['total_drivers'],
      'total_active_drivers' => $drivers['total_active_drivers'],
      'total_inactive_drivers' => $drivers['total_inactive_drivers'],
      'total_trip' => $trips['total_trip'],
      'total_new_trip' => $trips['total_new_trip'],
      'total_completed_trip' => $trips['total_completed_trip'],
      'total_running_trip' => $trips['total_running_trip'],
      'total_canceled_trip' => $trips['total_canceled_trip'],
      'total_declined_trip' => $trips['total_declined_trip'],
      'today_revenue' => $revenue['today_revenue'],
      'total_revenue' => $revenue['total_revenue'],
      'today_commission' => $commissions['today_commission'],
      'total_commission' => $commissions['total_commission'],
      'today_expenses' => $expenses['today_expenses'],
      'total_expenses' => $expenses['total_expenses'],
      'overall_savings' => $savings['overall_savings'],
      'overall_savings_withdrawals' => $savings['overall_savings_withdrawals'],
      'total_driver_savings' => $savings['total_driver_savings'],
      'total_motorboy_savings' => $savings['total_motorboy_savings'],
      'total_driver_withdrawals' => $savings['total_driver_withdrawals'],
      'total_motorboy_withdrawals' => $savings['total_motorboy_withdrawals'],
      'previous_week_overall_savings' => $savings['previous_week_overall_savings'],
      'previous_week_overall_savings_withdrawals' => $savings['previous_week_overall_savings_withdrawals'],
      'previous_week_driver_savings' => $savings['previous_week_driver_savings'],
      'previous_week_motorboy_savings' => $savings['previous_week_motorboy_savings'],
      'previous_week_driver_withdrawals' => $savings['previous_week_driver_withdrawals'],
      'previous_week_motorboy_withdrawals' => $savings['previous_week_motorboy_withdrawals'],
      'merchant' => [],
    ];

    $data['net_revenue'] = $data['total_revenue'] - $data['total_commission'];
    return view('/content/dashboard/dashboard-analytics', $data);
  }
}
