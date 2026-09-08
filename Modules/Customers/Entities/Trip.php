<?php

namespace Modules\Customers\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "driver_id",
        "pickup_location_id",
        "drop_location_id",
        "total_cost",
        "cost_of_sand",
        "road_money",
        "client_name",
        "trip_generated_at",
        "clock_in_time",
        "clock_out_time",
        "pick_up_datetime",
        "driver_commission",
        "piclocation",
        "status",
        "payment_id",
        "total_time",
        "client_id",
        'created_at',
        'updated_at',
    ];

    protected static function newFactory()
    {
        return \Modules\Customers\Database\factories\TripFactory::new();
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

    public function payment()
    {
        $payment = TripPayment::find($this->payment_id);
        return $payment;
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

    public function truck_number()
    {
        $driver = Driver::find($this->driver_id);
        return $driver ? $driver->vehicle_id : null;
    }

    public function total_trip_cost()
    {
        return $this->total_cost + ($this->cost_of_sand ?? 0) + ($this->road_money ?? 0);
    }

    public function send_trip_request_notification()
    {
        $driver = Driver::where(['id' => $this->driver_id])->whereNotNull('device_token')->select('device_token')->first();
        if ($driver && $driver->device_token) {
            $SERVER_API_KEY = config('app.fcm_server_key');

            $body = $this->client_name . " has requested a trip to " . $this->drop_location() . " for " . $this->total_cost . " NGN.";
            $data = [
                "registration_ids" => [$driver->device_token],
                "notification" => [
                    "title" => "New Trip Request",
                    "body" => $body,
                    'icon' => "https://pwa.fleetng.com/images/icons/dashboard-truck.png",
                    "click_action" => "/trip-requests",
                ]
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
        }

        return TripRequest::create(['client_id' => $this->client_id, 'driver_id' => $this->driver_id, 'trip_id' => $this->id]);  // create notification data 
    }
}
