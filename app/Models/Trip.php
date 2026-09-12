<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

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
        "payment_confirm_by_bank_transfer",
        "payment_confirmed_by_pos",
        "total_time",
        "client_id",
        'created_at',
        'updated_at',
    ];

    public function driver()
    {
        return $this->belongsTo('App\Models\Driver', 'driver_id', 'id');
    }

    public function client()
    {
        return $this->belongsTo('App\Models\Customer', 'client_id', 'id');
    }


    public function dropLocation()
    {
        return $this->belongsTo('App\Models\DropLocation', 'drop_location_id', 'id');
    }


    public function pickupLocation()
    {
        return $this->belongsTo('App\Models\PickupLocation', 'pickup_location_id', 'id');
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
        return $location ? $location->location_name ?? $location->location : null;
    }

    public function payment()
    {
        $payment = TripPayment::find($this->payment_id);
        return $payment;
    }

    public function client_info()
    {
        $client = Customer::select('id', 'first_name', 'last_name', 'phone_number', 'email')->where('id', $this->client_id)->first();
        return $client;
    }

    public function driver_info()
    {
        $driver = Driver::find($this->driver_id);
        return $driver ? $driver->full_name : null;
    }

    public function request_status()
    {
        $result = TripRequest::where(['driver_id' => $this->driver_id, 'trip_id' => $this->id])->first();
        return $result ? $result->status : 1;
    }

    public function merchant_info()
    {
        $driver = Driver::find($this->driver_id);
        if ($driver && $driver->merchant_id) {
            $merchant = User::find($driver->merchant_id);
            return $merchant ? $merchant->full_name : null;
        }
        return null;
    }

    public function merchant()
    {
        $driver = Driver::find($this->driver_id);
        if ($driver && $driver->merchant_id) {
            $merchant = User::find($driver->merchant_id);
            return $merchant ? $merchant : null;
        }
        return null;
    }

    public function total_trip_cost()
    {
        return $this->total_cost + ($this->cost_of_sand ?? 0) + ($this->road_money ?? 0);
    }

    public function truck_number()
    {
        $driver = Driver::find($this->driver_id);
        return $driver ? $driver->vehicle_id : null;
    }

    public function send_trip_request_notification()
    {
        $driver = Driver::where(['id' => $this->driver_id])->whereNotNull('device_token')->select('device_token')->first();
        if ($driver && $driver->device_token && !app()->environment(['local','testing'])) {
            $SERVER_API_KEY = config('app.fcm_server_key');

            $body = $this->client_name . " has requested a trip to " . $this->drop_location() . " for " . $this->total_cost . " NGN.";
            $data = [
                "to" => $driver->device_token,
                "notification" => [
                    "title" => "New Trip Request",
                    "body" => $body,
                    'icon' => "https://pwa.fleetng.com/images/icons/dashboard-truck.png",
                    "click_action" => "/trip-requests",
                ],
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




    public function send_clocked_in_notification()
    {
        $client = Customer::where(['id' => $this->client_id])->whereNotNull('device_token')->select('device_token')->first();
        if ($client && $client->device_token && !app()->environment(['local','testing'])) {
            $SERVER_API_KEY = config('app.fcm_server_key');

            $body = "Your trip from " . $this->pickup_location_alias() . " to " . $this->drop_location() . " has been clocked in.";
            $data = [
                "to" => $client->device_token,
                "notification" => [
                    "title" => "Trip has been clocked in",
                    "body" => $body,
                    'icon' => "https://pwa.fleetng.com/images/icons/dashboard-truck.png",
                    "click_action" => "/trip/" . $this->id . "/view",
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
    }

    public function send_clocked_out_notification()
    {
        $client = Customer::where(['id' => $this->client_id])->whereNotNull('device_token')->select('device_token')->first();
        if ($client && $client->device_token && !app()->environment(['local','testing'])) {
            $SERVER_API_KEY = config('app.fcm_server_key');

            $body = "Your trip from " . $this->pickup_location_alias() . " to " . $this->drop_location() . " has been clocked out.";
            $data = [
                "to" => $client->device_token,
                "notification" => [
                    "title" => "Trip has been clocked out",
                    "body" => $body,
                    'icon' => "https://pwa.fleetng.com/images/icons/dashboard-truck.png",
                    "click_action" => "/trip/" . $this->id . "/view",
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
    }
}
