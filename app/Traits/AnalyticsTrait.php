<?php

namespace App\Traits;

use App\Models\Driver;
use App\Models\Expense;
use App\Models\Trip;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
trait AnalyticsTrait
{
    public function getUsersCount()
    {
        $user = Auth::user();

        $data = [
            'total_users' => 0,
            'total_active_users' => 0,
            'total_inactive_users' => 0,
        ];

        // get users count for superadmin and admin type role 
        if ($user->user_type === 1 || $user->user_type === 2) {
            $data['total_users'] = User::where('user_type', 3)->count();
            $data['total_active_users'] = User::where('user_type', 3)->where('is_active', 1)->count();
            $data['total_inactive_users'] = User::where('user_type', 3)->where('is_active', 0)->count();
        }

        return $data;
    }

    public function getDriversCount()
    {
        $user = Auth::user();

        $data = [
            'total_drivers' => 0,
            'total_active_drivers' => 0,
            'total_inactive_drivers' => 0,
        ];

        // get drivers count for superadmin and admin type role 
        if ($user->user_type === 1 || $user->user_type === 2) {
            $data['total_drivers'] = Driver::count();
            $data['total_active_drivers'] = Driver::where('is_active', 1)->count();
            $data['total_inactive_drivers'] = Driver::where('is_active', 0)->count();
        }

        // get drivers count for user role 
        if ($user->user_type === 3) {
            $driver_ids = $user->drivers_ids()->toArray(); // drivers ids
            $data['total_drivers'] = Driver::whereIn('id', $driver_ids)->count();
            $data['total_active_drivers'] = Driver::whereIn('id', $driver_ids)->where('is_active', 1)->count();
            $data['total_inactive_drivers'] = Driver::whereIn('id', $driver_ids)->where('is_active', 0)->count();
        }

        return $data;
    }

    public function getTripStats()
    {
        $user = Auth::user();

        $data = [
            'total_trip' => 0,
            'total_new_trip' => 0,
            'total_completed_trip' => 0,
            'total_running_trip' => 0,
            'total_canceled_trip' => 0,
            'total_declined_trip' => 0,
        ];

        // get drivers count for superadmin and admin type role 
        if ($user->user_type === 1 || $user->user_type === 2) {
            $data['total_trip'] = Trip::count();
            $data['total_new_trip'] = Trip::where('status', 1)->count();
            $data['total_running_trip'] = Trip::where('status', 2)->count();
            $data['total_completed_trip'] = Trip::where('status', 3)->count();
            $data['total_canceled_trip'] = Trip::where('status', 4)->count();
            $data['total_declined_trip'] = Trip::where('status', 5)->count();
        }

        // get drivers count for user role
        if ($user->user_type === 3) {
            $driver_ids = $user->drivers_ids()->toArray(); // drivers ids
            $data['total_trip'] = Trip::whereIn('driver_id', $driver_ids)->count();
            $data['total_new_trip'] = Trip::whereIn('driver_id', $driver_ids)->where('status', 1)->count();
            $data['total_running_trip'] = Trip::whereIn('driver_id', $driver_ids)->where('status', 2)->count();
            $data['total_completed_trip'] = Trip::whereIn('driver_id', $driver_ids)->where('status', 3)->count();
            $data['total_canceled_trip'] = Trip::whereIn('driver_id', $driver_ids)->where('status', 4)->count();
            $data['total_declined_trip'] = Trip::whereIn('driver_id', $driver_ids)->where('status', 5)->count();
        }

        return $data;
    }

    public function getRevenue()
    {
        $user = Auth::user();

        $data = [
            'today_revenue' => 0,
            'total_revenue' => 0,
        ];

        // get trip revenue for superadmin and admin type role 
        if ($user->user_type === 1 || $user->user_type === 2) {
            $allTrips = Trip::whereYear('created_at', date('Y'))->whereNotNull('clock_out_time')->pluck('total_cost')->toArray();
            $total_revenue = Trip::whereYear('created_at', date('Y'))->whereDate('created_at',  Carbon::today())->whereNotNull('clock_out_time')->pluck('total_cost')->toArray();
            $data['today_revenue'] = array_sum($total_revenue);
            $data['total_revenue'] = array_sum($allTrips);
        }

        // get trip revenue for user role 
        if ($user->user_type === 3) {
            $driver_ids = $user->drivers_ids()->toArray(); // drivers ids
            $allTrips = Trip::whereYear('created_at', date('Y'))->whereIn('driver_id', $driver_ids)->whereNotNull('clock_out_time')->pluck('total_cost')->toArray();
            $total_revenue = Trip::whereYear('created_at', date('Y'))->whereIn('driver_id', $driver_ids)->whereDate('created_at',  Carbon::today())->whereNotNull('clock_out_time')->pluck('total_cost')->toArray();
            $data['today_revenue'] = array_sum($total_revenue);
            $data['total_revenue'] = array_sum($allTrips);
        }

        return $data;
    }

    
    public function getCommissions()
    {
        $user = Auth::user();

        $data = [
            'today_commission' => 0,
            'total_commission' => 0,
        ];

        // get trip revenue for superadmin and admin type role 
        if ($user->user_type === 1 || $user->user_type === 2) {
            $allTrips = Trip::whereYear('created_at', date('Y'))->whereNotNull('clock_out_time')->pluck('driver_commission')->toArray();
            $total_commission = Trip::whereYear('created_at', date('Y'))->whereDate('created_at',  Carbon::today())->whereNotNull('clock_out_time')->pluck('driver_commission')->toArray();
            $data['today_commission'] = array_sum($total_commission);
            $data['total_commission'] = array_sum($allTrips);
        }

        // get trip revenue for user role 
        if ($user->user_type === 3) {
            $driver_ids = $user->drivers_ids()->toArray(); // drivers ids
            $allTrips = Trip::whereYear('created_at', date('Y'))->whereIn('driver_id', $driver_ids)->whereNotNull('clock_out_time')->pluck('driver_commission')->toArray();
            $total_commission = Trip::whereYear('created_at', date('Y'))->whereIn('driver_id', $driver_ids)->whereDate('created_at',  Carbon::today())->whereNotNull('clock_out_time')->pluck('driver_commission')->toArray();
            $data['today_commission'] = array_sum($total_commission);
            $data['total_commission'] = array_sum($allTrips);
        }

        return $data;
    }
    
    public function getExpenses()
    {
        $user = Auth::user();

        $data = [
            'today_expenses' => 0,
            'total_expenses' => 0,
        ];

        // get trip revenue for superadmin and admin type role 
        if ($user->user_type === 1 || $user->user_type === 2) {
            $allExpense = Expense::whereYear('created_at', date('Y'))->pluck('item_cost')->toArray();
            $total_expenses = Expense::whereYear('created_at', date('Y'))->whereDate('created_at',  Carbon::today())->pluck('item_cost')->toArray();
            $data['today_expenses'] = array_sum($total_expenses);
            $data['total_expenses'] = array_sum($allExpense);
        }

        // get trip revenue for user role 
        if ($user->user_type === 3) {
            $driver_ids = $user->drivers_ids()->toArray(); // drivers ids
            $allExpense = Expense::whereYear('created_at', date('Y'))->whereIn('driver_id', $driver_ids)->pluck('item_cost')->toArray();
            $total_expenses = Expense::whereYear('created_at', date('Y'))->whereIn('driver_id', $driver_ids)->whereDate('created_at',  Carbon::today())->pluck('item_cost')->toArray();
            $data['today_expenses'] = array_sum($total_expenses);
            $data['total_expenses'] = array_sum($allExpense);
        }
        
        return $data;
    }

    public function getSavingsAnalytics()
    {
        $user = Auth::user();
        $data = [
            'overall_savings' => 0,
            'overall_savings_withdrawals' => 0,
            'total_driver_savings' => 0,
            'total_motorboy_savings' => 0,
            'total_driver_withdrawals' => 0,
            'total_motorboy_withdrawals' => 0,
            'previous_week_overall_savings' => 0,
            'previous_week_overall_savings_withdrawals' => 0,
            'previous_week_driver_savings' => 0,
            'previous_week_motorboy_savings' => 0,
            'previous_week_driver_withdrawals' => 0,
            'previous_week_motorboy_withdrawals' => 0,
        ];

        $driverIds = null;
        if ($user->user_type === 3) {
            $driverIds = $user->drivers_ids()->toArray();
        }

        $driverSavings = $this->sumExistingColumns('drivers', ['savings_balance', 'saving_balance', 'savings', 'wallet_balance'], 'id', $driverIds);
        $driverWithdrawals = $this->sumExistingColumns('drivers', ['savings_withdrawals', 'saving_withdrawals', 'withdrawals', 'withdrawal_balance'], 'id', $driverIds);
        $motorboySavings = $this->sumExistingColumns('motorboys', ['savings_balance', 'saving_balance', 'savings', 'wallet_balance']);
        $motorboyWithdrawals = $this->sumExistingColumns('motorboys', ['savings_withdrawals', 'saving_withdrawals', 'withdrawals', 'withdrawal_balance']);

        $data['total_driver_savings'] = $driverSavings;
        $data['total_driver_withdrawals'] = $driverWithdrawals;
        $data['total_motorboy_savings'] = $motorboySavings;
        $data['total_motorboy_withdrawals'] = $motorboyWithdrawals;
        $data['overall_savings'] = $driverSavings + $motorboySavings;
        $data['overall_savings_withdrawals'] = $driverWithdrawals + $motorboyWithdrawals;

        return $data;
    }

    private function sumExistingColumns(string $table, array $columns, ?string $scopeColumn = null, ?array $scopeIds = null): float
    {
        if (!Schema::hasTable($table)) {
            return 0;
        }

        foreach ($columns as $column) {
            if (!Schema::hasColumn($table, $column)) {
                continue;
            }

            $query = DB::table($table);
            if (Schema::hasColumn($table, 'deleted_at')) {
                $query->whereNull('deleted_at');
            }
            if ($scopeColumn && is_array($scopeIds)) {
                if (count($scopeIds) === 0) {
                    return 0;
                }
                $query->whereIn($scopeColumn, $scopeIds);
            }

            return (float) $query->sum($column);
        }

        return 0;
    }
}
