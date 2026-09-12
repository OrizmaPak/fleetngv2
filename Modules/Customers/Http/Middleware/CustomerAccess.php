<?php

namespace Modules\Customers\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Customers\Entities\Customer;
use Modules\Customers\Entities\DraftTrip;
use Modules\Customers\Entities\Trip;

class CustomerAccess
{
    public function handle(Request $request, Closure $next)
    {
        $customer = $request->user();
        abort_unless($customer, 401);
        abort_unless($customer instanceof Customer && $customer->is_active, 403);
        $id = $request->route('id');
        if ($id !== null) {
            $model = strpos($request->path(), '/draft/trips/') !== false ? DraftTrip::class : Trip::class;
            $model::where('client_id', $customer->id)->findOrFail($id);
        }
        if ($request->is('api/customer/trips/payment-link', 'customer-portal/api/trips/payment-link')) {
            $data = $request->validate(['trip_ids' => 'required|array|min:1|max:100', 'trip_ids.*' => 'required|integer|distinct']);
            $count = Trip::where('client_id', $customer->id)->whereIn('id', $data['trip_ids'])->count();
            abort_unless($count === count($data['trip_ids']), 404);
        }
        return $next($request);
    }
}
