<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TripPayment extends Model
{
    use HasFactory;

    protected $table = "trip_payments";

    protected $fillable = [
        "trip_id",
        "customer_id",
        "status",
        "amount",
        "transaction_id",
        "payment_type",
        "reference_code"
    ];

    public function trip()
    {
        $trip = Trip::select('id', 'pickup_location_id', 'drop_location_id', 'created_at', 'total_cost', 'cost_of_sand', 'road_money')->where(['id' => $this->trip_id])->first();
        if ($trip) {
            $trip->pickup_location = $trip->pickup_location();
            $trip->pickup_location_alias = $trip->pickup_location_alias();
            $trip->drop_location = $trip->drop_location();
            $trip->total_trip_cost = $trip->total_trip_cost();
        }

        return $trip;
    }
}
