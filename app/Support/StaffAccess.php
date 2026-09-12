<?php

namespace App\Support;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\PickupLocation;

class StaffAccess
{
    public static function staff($user)
    {
        abort_unless($user instanceof User && $user->is_active && in_array((int) $user->user_type, [1,2,3,4,5], true), 403);
    }

    public static function global($user) { return in_array((int) $user->user_type, [1,2], true); }

    public static function drivers($user)
    {
        self::staff($user);
        $query = Driver::query();
        if (self::global($user)) return $query;
        if ($user->is_payment_user || (int) $user->user_type === 5) return $query->where('merchant_id', $user->merchant_assigned ?: -1);
        return (int) $user->user_type === 4 ? $query->where('merchant_id', $user->id) : $query->where('user_id', $user->id);
    }

    public static function trips($user) { return Trip::whereIn('driver_id', self::drivers($user)->select('id')); }

    public static function api($method, $request)
    {
        if (in_array($method, ['forgot_password','admin_login','superadmin_login'], true)) return;
        $user = $request->user();
        self::staff($user);
        if ($method === 'change_password') { abort_unless((int) $request->user_id === (int) $user->id, 403); return; }
        if (strpos($method, 'merchant') !== false) {
            if ($method === 'get_merchant_detail' && !$request->user_id) return;
            abort_unless((int) $user->user_type === 1, 403);
            if ($request->user_id) abort_unless(User::where('user_type',4)->whereKey($request->user_id)->exists(),404);
            return;
        }
        if (strpos($method, 'user') !== false) {
            abort_unless(self::global($user),403);
            if ($request->user_id) abort_unless(User::where('user_type',3)->whereKey($request->user_id)->exists(),404);
            return;
        }
        $read = strpos($method, 'get_') === 0;
        if (!$read) abort_if($user->is_payment_user || (int) $user->user_type === 5,403);
        if (strpos($method, 'driver') !== false && $request->driver_id) abort_unless(self::drivers($user)->whereKey($request->driver_id)->exists(),404);
        if ($method === 'get_driver_detail' && !$request->driver_id && $request->vehicle_id) {
            $driver = Driver::where('vehicle_id',$request->vehicle_id)->first();
            if ($driver) abort_unless(self::drivers($user)->whereKey($driver->id)->exists(),404);
        }
        if ($request->trip_id) abort_unless(self::trips($user)->whereKey($request->trip_id)->exists(),404);
        if ($request->location_id) {
            $query = PickupLocation::whereKey($request->location_id);
            if (!self::global($user)) $query->where('user_id',$user->id);
            abort_unless($query->exists(),404);
        }
        if (!$read && !self::global($user) && $request->user_id) abort_unless((int)$request->user_id === (int)$user->id,403);
    }
}
