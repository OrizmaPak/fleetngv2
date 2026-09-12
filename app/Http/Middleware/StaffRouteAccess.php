<?php

namespace App\Http\Middleware;

use App\Models\Expense;
use App\Models\PickupLocation;
use App\Support\StaffAccess;
use Closure;

class StaffRouteAccess
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        StaffAccess::staff($user);
        $method = $request->route()->getActionMethod();
        $controller = $request->route()->getActionName();
        if (strpos($controller,'Admin\\TrackingController@') !== false && !config('integrations.tracking_enabled')) {
            if ($request->ajax()) return response()->json(['message'=>'Tracking provider is not connected.','data'=>[]],503);
            return response()->view('admin.provider-unavailable');
        }
        $lookup = in_array($method,['merchant_list_data','merchant_data','user_data'],true);
        if ($request->is('superadmin/*') && !$lookup) abort_unless((int)$user->user_type === 1,403);
        if ($user->is_payment_user || (int)$user->user_type === 5) {
            abort_unless(in_array($method,['trip_list','trip_list_detail','trip_list_detail_filter','trip_detail','trip_confirm_pos','trip_bank_payment_received','bulk_trip_bank_payment_received','bulk_trip_pos_payment_received','payment_list','trip_invoice_pdf_download','account_settings','change_password','send_sms_payment_alert_to_client'],true),403);
        }
        $id = $request->route('id');
        if (strpos($controller,'Admin\\DriverController@') !== false) {
            $driverId = $id ?: $request->input('driver_id', $request->input('id'));
            if ($driverId) abort_unless(StaffAccess::drivers($user)->whereKey($driverId)->exists(),404);
        }
        if (strpos($controller,'Admin\\TripController@') !== false || $method === 'trip_invoice_pdf_download' || strpos($controller,'SMSController@') !== false) {
            $tripId = $id ?: $request->input('trip_id');
            if ($tripId) abort_unless(StaffAccess::trips($user)->whereKey($tripId)->exists(),404);
            foreach (['transfer_trip_ids','pos_trip_ids'] as $field) {
                if (!$request->has($field)) continue;
                $ids = explode(',',(string)$request->input($field));
                $request->merge(['checked_trip_ids'=>$ids]);
                $request->validate(['checked_trip_ids'=>'required|array|min:1|max:100','checked_trip_ids.*'=>'required|integer|distinct']);
                abort_unless(StaffAccess::trips($user)->whereIn('id',$ids)->count() === count($ids),404);
            }
            if ($request->input('driver_id')) abort_unless(StaffAccess::drivers($user)->whereKey($request->input('driver_id'))->exists(),404);
        }
        if (strpos($controller,'Admin\\LocationController@') !== false) {
            $locationId = $id ?: $request->input('location_id', $request->input('id'));
            if ($locationId) {
                $query = PickupLocation::whereKey($locationId);
                if (!StaffAccess::global($user)) $query->where('user_id',$user->id);
                abort_unless($query->exists(),404);
            }
        }
        if ($request->input('expense_id')) {
            abort_unless(Expense::whereIn('driver_id',StaffAccess::drivers($user)->select('id'))->whereKey($request->input('expense_id'))->exists(),404);
        }
        return $next($request);
    }
}
