<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TripPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        "trip_id",
        "customer_id",
        "status",
        "amount",
        "transaction_id",
        "payment_type",
        "reference_code",
        "payment_confirmed_by"
    ];


    public function trip()
    {
        $trip = Trip::select('id', 'client_id', 'pickup_location_id', 'drop_location_id', 'created_at')->where(['id' => $this->trip_id])->first();
        if ($trip) {
            $trip->pickup_location = $trip->pickup_location();
            $trip->drop_location = $trip->drop_location();
            $trip->client = $trip->client_info();
        }

        return $trip;
    }
}
