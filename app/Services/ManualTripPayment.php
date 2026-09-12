<?php

namespace App\Services;

use App\Models\TripPayment;
use App\Support\StaffAccess;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ManualTripPayment
{
    public function record($user, array $ids, $method)
    {
        StaffAccess::staff($user);
        abort_unless(($user->is_payment_user || (int)$user->user_type === 5) && $user->merchant_assigned,403);
        Validator::make(['ids'=>$ids],['ids'=>'required|array|min:1|max:100','ids.*'=>'required|integer|distinct'])->validate();
        abort_unless(in_array($method,['confirmed_pos','confirmed_bank_transfer'],true),422);
        return DB::transaction(function () use ($user,$ids,$method) {
            $trips = StaffAccess::trips($user)->whereIn('id',$ids)->orderBy('id')->lockForUpdate()->get();
            abort_unless($trips->count() === count($ids),404);
            foreach ($trips as $trip) {
                if (in_array((int)$trip->status,[4,5],true)) throw ValidationException::withMessages(['trip'=>'Canceled or declined trips cannot be paid.']);
                if ($trip->payment_id) {
                    $paid = TripPayment::find($trip->payment_id);
                    if (!$paid || $paid->payment_type !== $method || (int)$paid->payment_confirmed_by !== (int)$user->id) throw ValidationException::withMessages(['trip'=>'A selected trip has already been paid. Refresh the list.']);
                }
            }
            foreach ($trips as $trip) {
                if ($trip->payment_id) continue;
                $payment = TripPayment::create(['trip_id'=>$trip->id,'customer_id'=>$trip->client_id,'status'=>'successful','amount'=>$trip->total_trip_cost(),'payment_type'=>$method,'payment_confirmed_by'=>$user->id]);
                $trip->update(['payment_id'=>$payment->id, $method === 'confirmed_pos' ? 'payment_confirmed_by_pos' : 'payment_confirm_by_bank_transfer'=>1]);
            }
            return $trips;
        });
    }
}
