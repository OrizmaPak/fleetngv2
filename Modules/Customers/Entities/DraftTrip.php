<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DraftTrip extends Model
{
    use HasFactory;
    protected $table = "draft_trips";
    protected $fillable = [
        "driver_id",
        "client_id",
        "pickup_location_id",
        "drop_location_id",
        "total_cost",
        "client_name",
        "pick_up_datetime",
    ];

    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\DraftTripFactory::new();
    }


    public function drop_location()
    {
        $location = DropLocation::find($this->drop_location_id);
        return $location ? $location->location : null;
    }

    public function pickup_location()
    {
        $location = PickupLocation::find($this->pickup_location_id);
        return $location ? $location->location : null;
    }

    public function pickup_location_alias()
    {
        $location = PickupLocation::find($this->pickup_location_id);
        return $location ? $location->location_name : null;
    }

    public function confirm()
    {
        $trip = Trip::create($this->toArray());
        $trip->send_trip_request_notification(); // send notification to driver
        return $trip;
    }

    public function driver()
    {
        $driver = Driver::find($this->driver_id);
        return $driver ? $driver->full_name : null;
    }

    public function merchant()
    {
        $driver = Driver::find($this->driver_id);
        if ($driver && $driver->merchant_id) {
            $merchant = User::find($driver->merchant_id);
            return $merchant ? $merchant->merchant_name : null;
        }
        return null;
    }

    public function merchant_id()
    {
        $driver = Driver::find($this->driver_id);
        if ($driver && $driver->merchant_id) {
            $merchant = User::find($driver->merchant_id);
            return $merchant ? $merchant->id : null;
        }
        return null;
    }

    public function truck_number()
    {
        $driver = Driver::find($this->driver_id);
        return $driver ? $driver->vehicle_id : null;
    }
}
