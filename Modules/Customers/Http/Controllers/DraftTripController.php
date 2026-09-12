<?php

namespace Modules\Customers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Modules\Customers\Entities\DropLocation;
use Modules\Customers\Entities\PickupLocation;
use Modules\Customers\Entities\DraftTrip;

class DraftTripController extends Controller
{

    public function allTrips(Request $request)
    {
        try {
            $customer = $request->user();
            $statusText = [1 => 'New', 2 => 'Running', 3 => 'Completed', 4 => 'Canceled'];  // status indications

            $trips = DraftTrip::where(['client_id' => $customer->id])->get()->each(function ($item) use ($statusText) {
                $item->drop_location = $item->drop_location();
                $item->pickup_location = $item->pickup_location();
                $item->merchant = $item->merchant();
                $item->driver = $item->driver();
                $item->truck_number = $item->truck_number();
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
                'pickup_location' => 'required|exists:pickup_locations,id',
                'pickup_datetime' => 'required|date|after_or_equal:today',
                'drop_off_location' => 'required|max:255',
                'cost' => 'required|integer|min:0',
            ];

            $message = [
                'driver.exists' => 'The selected driver is invalid.',
            ];
            $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

            if ($validator->fails()) {
                return response()->badRequest($validator->errors()->first(), $validator->errors());
            }


            // $pickup_location_id = PickupLocation::create(['location' => $request->pickup_location])->id;  // insert pickup location in database
            $drop_location_id = DropLocation::create(['location' => $request->drop_off_location])->id; // insert drop-off location in database

            $data = [
                'driver_id' => $request->driver,
                'pickup_location_id' => $request->pickup_location,
                'drop_location_id' => $drop_location_id,
                'pick_up_datetime' => $request->pickup_datetime,
                'total_cost' => $request->cost,
                'client_name' => $request->user()->full_name,
                'client_id' => $request->user()->id,
            ];

            $trip = DraftTrip::create($data);  // create trip data in database
            $trip->drop_location = $trip->drop_location();
            $trip->pickup_location = $trip->pickup_location();

            return response()->success("Trip confirmed successfully!", $trip);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function show($id)
    {
        try {
            $trip = DraftTrip::where('id', $id)->first();
            if ($trip) {
                $trip->drop_location = $trip->drop_location();  // append drop-off location name 
                $trip->pickup_location = $trip->pickup_location();  // append pickup location name 
                $trip->pickup_location_alias = $trip->pickup_location_alias();  // append pickup location alias 
                $trip->status = 'Draft';
                $trip->merchant = $trip->merchant();
                $trip->merchant_id = $trip->merchant_id();
                $trip->driver = $trip->driver();
                $trip->truck_number = $trip->truck_number();
            }

            return response()->success("", $trip);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $validate = [
                'driver' => 'exists:drivers,id',
                'pickup_location' => 'required|exists:pickup_locations,id',
                'pickup_datetime' => 'required|date|after_or_equal:today',
                'drop_off_location' => 'required|max:255',
                'cost' => 'required|integer|min:0',
            ];

            $message = [
                'driver.exists' => 'The selected driver is invalid.',
            ];
            $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

            if ($validator->fails()) {
                return response()->badRequest($validator->errors()->first(), $validator->errors());
            }

            $trip = DraftTrip::where('client_id', $request->user()->id)->find($id);
            if (!$trip) return response()->badRequest('Invalid trip id.');
            // Locations may be shared. Never mutate a location supplied by the caller.
            if ($trip->drop_location() === $request->drop_off_location) {
                $drop_location_id = $trip->drop_location_id;
            } else {
                $drop_location_id = DropLocation::create(['location' => $request->drop_off_location])->id; // insert drop-off location in database
            }

            $data = [
                'driver_id' => $request->driver,
                'pickup_location_id' => $request->pickup_location,
                'drop_location_id' => $drop_location_id,
                'pick_up_datetime' => $request->pickup_datetime,
                'total_cost' => $request->cost,
                'client_name' => $request->user()->full_name,
                'client_id' => $request->user()->id,
            ];

            if ($id) {
                $trip =  DraftTrip::find($id);  // update the draft trip details
                if (!$trip) {
                    return response()->badRequest('Invalid trip id.');
                }
                $trip->update($data);
            } else {
                $trip = DraftTrip::create($data);  // create trip data in database
            }

            $trip->drop_location = $trip->drop_location();
            $trip->pickup_location = $trip->pickup_location();

            return response()->success("Trip confirmed successfully!", $trip);
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

        $trip = DraftTrip::where('id', $id)->first();  // get trip data from database

        if (!$trip) {
            return response()->badRequest("Trip not found.");
        }

        $trip->driver_id = $request->driver;
        $trip->save();  // save driver id in trips table
        return response()->success("Driver added to your Trip.", $trip);
    }


    public function tripConfirm(Request $request, $id)
    {
        try {
            return DB::transaction(function () use ($request, $id) {
                $trip = DraftTrip::where('client_id', $request->user()->id)->lockForUpdate()->find($id);
                if (!$trip) return response()->badRequest('This draft was not found or has already been confirmed.');
                if (!$trip->driver_id) return response()->badRequest('Choose a driver before confirming. You can still save this booking as a draft.');
                if (strtotime($trip->pick_up_datetime) < strtotime('today')) return response()->badRequest('Update the pickup date before confirming this draft.');
                $response = $trip->confirm();
                $trip->delete();
                return response()->success('Trip confirmed successfully.', $response);
            });
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }
}
