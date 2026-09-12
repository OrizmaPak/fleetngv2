<?php

namespace Modules\Customers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Customers\Entities\DropLocation;
use Modules\Customers\Entities\PickupLocation;
use Modules\Customers\Entities\Trip;

class TripController extends Controller
{

    public function allTrips(Request $request)
    {
        try {
            $customer = $request->user();
            $statusText = [1 => 'New', 2 => 'Running', 3 => 'Completed', 4 => 'Canceled', 5 => 'Declined'];  // status indications

            $trips = Trip::where(['client_id' => $customer->id])->orderBy('pick_up_datetime', 'desc')->get()->each(function ($item) use ($statusText) {
                $item->drop_location = $item->drop_location();
                $item->pickup_location = $item->pickup_location();
                $item->location_alias = $item->pickup_location_alias();
                $item->merchant = $item->merchant();
                $item->driver = $item->driver();
                $item->truck_number = $item->truck_number();
                $item->total_trip_cost = $item->total_trip_cost();
                $item->payment = $item->payment();
                if (!$item->pick_up_datetime) {
                    $item->pick_up_datetime = $item->trip_generated_at;
                }
                $item->status = $statusText[$item->status];
            });  // get all trips data

            return response()->success("", $trips);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function store(Request $request)
    {

        try {
            $validate = [
                'driver' => 'exists:drivers,id',
                'pickup_location' => 'required|max:255',
                'pickup_datetime' => 'required|date|after_or_equal:today',
                'drop_off_location' => 'required|max:255',
                'cost' => 'required|integer',
            ];

            $message = [
                'driver.exists' => 'The selected driver is invalid.',
            ];
            $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

            if ($validator->fails()) {
                return response()->badRequest($validator->errors()->first(), $validator->errors());
            }


            $pickup_location_id = PickupLocation::create(['location' => $request->pickup_location])->id;  // insert pickup location in database
            $drop_location_id = DropLocation::create(['location' => $request->drop_off_location])->id; // insert drop-off location in database

            $data = [
                'driver_id' => $request->driver,
                'pickup_location_id' => $pickup_location_id,
                'drop_location_id' => $drop_location_id,
                'pick_up_datetime' => $request->pickup_datetime,
                'total_cost' => $request->cost,
                'client_name' => $request->user()->full_name,
                'client_id' => $request->user()->id,
            ];

            $trip = Trip::create($data);  // create trip data in database
            $trip->drop_location = $trip->drop_location();
            $trip->pickup_location = $trip->pickup_location();
            $trip->send_trip_request_notification(); // send notification to driver
            return response()->success("Trip confirmed successfully!", $trip);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function show($id)
    {
        try {
            $trip = Trip::where('id', $id)->first();
            if (!$trip) {
                return response("Trip not found.", 404);
            }
            $statusText = [1 => 'New', 2 => 'Running', 3 => 'Completed', 4 => 'Canceled', 5 => 'Declined'];  // status indications
            $trip->drop_location = $trip->drop_location();  // append drop-off location name 
            $trip->pickup_location = $trip->pickup_location();  // append pickup location name 
            $trip->pickup_location_alias = $trip->pickup_location_alias();  // append pickup location alias 
            $trip->status = $statusText[$trip->status];
            $trip->merchant = $trip->merchant();
            $trip->driver = $trip->driver();
            $trip->total_trip_cost = $trip->total_trip_cost();
            $trip->truck_number = $trip->truck_number();
            $trip->payment = $trip->payment();
            return response()->success("", $trip);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function tripDriver(Request $request, $id)
    {
        $validate = [
            'driver' => 'required|exists:drivers,id',
        ];

        $message = [
            'driver.exists' => 'The selected driver is invalid.',
        ];
        $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

        if ($validator->fails()) {
            return response()->badRequest($validator->errors()->first(), $validator->errors());
        }

        $trip = Trip::where('client_id', $request->user()->id)->find($id);

        if (!$trip) {
            return response()->badRequest("Trip not found.");
        }

        $trip = \Illuminate\Support\Facades\DB::transaction(function () use ($request, $id) {
            $trip = Trip::where('client_id', $request->user()->id)->lockForUpdate()->findOrFail($id);
            abort_unless((int) $trip->status === 1, 422, 'Only new trips can change driver.');
            abort_unless(\App\Models\Driver::where('id', $request->driver)->where('is_active', 1)->exists(), 422, 'The selected driver is unavailable.');
            $trip->driver_id = $request->driver;
            $trip->save();
            return $trip;
        });
        return response()->success("Driver added to your Trip.", $trip);
    }

    public function pickupLocations()
    {
        try {
            $locations = PickupLocation::where('is_active', 1)->get();
            return response()->success("", $locations);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function tripSendNotification($tripId)
    {
        $trip = Trip::where('id', $tripId)->first();

        if (!$trip) {
            return response()->badRequest('Trip not found.');
        }

        if ($trip->status != 1) {
            return response()->badRequest('Please provide valid trip id.');
        }

        try {
            $trip->send_trip_request_notification(); // send notification to driver

            return response()->success("Notification has been sent.");
        } catch (\Exception $th) {
            info('------ Trip Send Notification Failed -------', [$th->getMessage()]);
            return response()->badRequest($th->getMessage());
        }
    }
}
