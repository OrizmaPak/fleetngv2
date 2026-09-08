<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TripRequest extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = ["trip_id", "client_id", "type", "driver_id", "is_read", "status"];

    public function trip()
    {
        $trip = Trip::select('id', 'pickup_location_id', 'drop_location_id', 'total_cost', 'created_at')->find($this->trip_id);
        if($trip){
            $trip->pickup_location = $trip->pickup_location_alias();
            $trip->drop_location = $trip->drop_location();
        }
        return $trip;
    }

    public function client()
    {
        $client = Customer::select('id', 'first_name', 'last_name', 'phone_number', 'email')->find($this->client_id);
        return $client;
    }
}
