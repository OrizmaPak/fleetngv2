<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DropLocation;
use App\Models\PickupLocation;
use App\Models\Trip;
use App\Models\User;
use App\Models\Driver;
use App\Models\Expense;
use App\Models\TripRequest;
use Illuminate\Http\Request;

class DriverController extends Controller
{

  public function testFunction($id)
  {
    $driver = Driver::find($id);
    return $driver->sendPushNotification();
  }

  public function testFunction2($id)
  {
    $driver = Driver::find($id);
    return $driver->sendPushNotification2();
  }

  public function testFunction3($id)
  {
    $driver = Driver::find($id);
    return $driver->sendPushNotification3();
  }

  public function testTripFunction(Request $request)
  {
    $trip_ids = explode(',', $request->trip_id);
    $trips = Trip::whereIn('id', $trip_ids)->get();
    foreach ($trips as $key => $trip) {
      $trip->send_trip_request_notification();
    }
    return $trips;
  }

  //function used for driver login
  public function driver_login(Request $request)
  {
    if ($request->phone && $request->auth_pin) {
      $driver = Driver::where('phone', $request->phone)
        ->where('auth_pin', $request->auth_pin)
        ->first();
      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {
        $response['success'] = true;
        $response['message'] = "Success! Driver found";
        if ($request->device_token) {
          $driver->saveDeviceToken($request->device_token);
        }

        $temp['driver_id']  = $driver->id;
        $temp['user_id']  = $driver->user_id;
        $temp['full_name']  = $driver->first_name . ' ' . $driver->last_name;
        $temp['photo']      = url('/') . '/' . $driver->photo;
        $temp['vehicle_id']  = $driver->vehicle_id;
        $temp['serial']  = $driver->device_serial_number;


        $response['data']   =   $temp;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  public function driver_logout(Request $request)
  {
    if ($request->driver_id) {
      $driver = Driver::find($request->driver_id);
      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {
        $response['success'] = true;
        $response['message'] = "Success! Driver logged out.";
        $driver->removeDeviceToken($request->device_token);
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to show driver total commission
  public function driver_total_commission(Request $request)
  {
    if ($request->driver_id) {
      $trip = Trip::whereYear('created_at', date('Y'))->where('driver_id', $request->driver_id)->where('status', 3)->pluck('driver_commission');
      $revenue = 0;
      foreach ($trip as $key => $value) {
        $revenue += $value;
      }

      $response['success'] = true;
      $response['message'] = "Success! Driver total commission found";
      $response['total_commission']   =   $revenue;
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to add new trip
  public function add_new_trip(Request $request)
  {
    if ($request->driver_id && $request->total_cost && $request->drop_location && $request->client_name) {
      $driver = Driver::where('id', $request->driver_id)->first();

      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {
        // checking if trip is already exists with status 1, if its exists then update it with droplocation 
        $oldTrip = Trip::where(['driver_id' => $request->driver_id, 'pickup_location_id' => $request->pick_location])
          ->where('status', 1)
          ->orderBy('id', 'desc');

        if ($oldTrip->exists()) {

          $oldTrip->update([
            'total_cost'       =>  $request->total_cost,
            'client_name'      =>  $request->client_name,
            'trip_generated_at' =>  now(),
          ]);

          $oldTrip = $oldTrip->first();
          // update drop location
          DropLocation::where('id', $oldTrip->drop_location_id)->update([
            'location' => $request->drop_location,
            // 'latitude' =>$latitude,
            // 'longitude'=>$longitude,
          ]);

          // if trip already creted
          if ($oldTrip != null) {

            $response['success'] = true;
            $response['message'] = "Success! Trip already created.";

            $temp['trip_id']  = $oldTrip->id;
            if ($oldTrip->dropLocation) $temp['drop_location']  = $oldTrip->dropLocation->location;

            if ($oldTrip->pickupLocation) $temp['pick_location']  = $oldTrip->pickupLocation->location_name;

            $temp['total_cost']  = $oldTrip->total_cost;
            $temp['client_name']  = $oldTrip->client_name;

            $response['trip_details'] = $temp;
          } else {

            $response['success'] = false;
            $response['message'] = "Error! Something went wrong, Please try agian!";
          }
        } else {

          // if trip is already not exists then create new one
          $drop_location = DropLocation::create([
            'location' => $request->drop_location,
            // 'latitude' =>$latitude,
            // 'longitude'=>$longitude,
          ]);


          if (empty($drop_location)) { // if drop location is not created in db
            return response(['success' => false, 'message' => 'Internal server error, Please try again!'], 500);
          } else {
            $d_location = $drop_location->id;
          }
          $trip = new Trip(); //creating new trip
          $trip->driver_id = $request->driver_id;
          $trip->drop_location_id = $d_location;
          $trip->pickup_location_id = $request->pick_location;
          $trip->total_cost = $request->total_cost;
          $trip->client_name = $request->client_name;
          $trip->trip_generated_at = now();
          $trip->clock_in_time = NULL;
          $trip->clock_out_time = NULL;

          $trip->save();
          $id = $trip->id;

          $response['success'] = true;
          $response['message'] = "Success! New trip created successfully.";

          $latest_trip = Trip::with('dropLocation', 'pickupLocation')->where('id', $id)->first();

          $temp['trip_id']  = $latest_trip->id;
          if ($latest_trip->dropLocation) {
            $temp['drop_location']  = $latest_trip->dropLocation->location;
          }
          if ($latest_trip->pickupLocation) {
            $temp['pick_location']  = $latest_trip->pickupLocation->location_name;
          }
          $temp['total_cost']  = $latest_trip->total_cost;
          $temp['client_name']  = $latest_trip->client_name;

          $response['trip_details'] = $temp;
        }
      }
    } else {

      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to start trip clock
  public function start_clock(Request $request)
  {
    if ($request->driver_id && $request->trip_id) {
      $driver = Driver::where('id', $request->driver_id)->first();

      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {

        $trip = Trip::where('id', $request->trip_id)->where('driver_id', $request->driver_id)->first();
        if (!$trip) {
          $response['success'] = false;
          $response['message'] = "Error! Trip not found.";
        } else {
          $trip->update(['clock_in_time' => now(), 'status' => 2]);

          TripRequest::where(['trip_id' => $request->trip_id, 'driver_id' => $request->driver_id])->delete(); // remove trip request from database
          $trip->send_clocked_in_notification();
          $response['success'] = true;
          $response['message'] = "Success! Trip clock started successfully.";
        }
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to stop trip clock
  public function stop_clock(Request $request)
  {

    if ($request->driver_id && $request->trip_id) {
      $driver = Driver::where('id', $request->driver_id)->first();

      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {

        $trip = Trip::where('id', $request->trip_id)->where('driver_id', $request->driver_id)->first();
        if (!$trip) {
          $response['success'] = false;
          $response['message'] = "Error! Trip not found.";
        } else {
          $driver_commission = round(($trip->total_cost * 10) / 100);

          Trip::where(['id' => $request->trip_id, 'driver_id' => $request->driver_id])->update(['clock_out_time' => now(), 'driver_commission' => $driver_commission, 'status' => 3]);

          $trip->send_clocked_out_notification();

          $response['success'] = true;
          $response['message'] = "Success! Trip clock stopped successfully.";
        }
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to show drop location
  public function show_drop_location()
  {
    $drop_location = DropLocation::all();

    if (isset($drop_location) && count($drop_location) > 0) {
      $response['success'] = true;
      $response['message'] = "Success! Drop location found.";


      foreach ($drop_location as $key => $value) {
        $temp['id']         = $value->id;
        $temp['location']       = $value->location;

        $response['data'][] = $temp;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Drop location not found.";
    }

    return json_encode($response);
  }

  //function used to show pickup location
  public function show_pickup_location()
  {
    $drop_location = PickupLocation::select('id', 'is_active', 'location', 'location_name', 'user_id', 'latitude', 'longitude')->get();
    if (isset($drop_location) && count($drop_location) > 0) {
      $response['success'] = true;
      $response['message'] = "Success! Pickup location found.";
      $response['data'] = $drop_location;
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Pickup location not found.";
    }

    return json_encode($response);
  }

  //function used to see the driver completed trip
  public function driver_completed_trip(Request $request)
  {
    // return $request->all();
    if ($request->driver_id && $request->date) {
      $driver = Driver::where('id', $request->driver_id)->first();

      if (empty($driver)) {
        $response['success'] = false;
        $response['message'] = "No completed Trip found for the selected date.";
      } else {

        $from_date = $request->date . ' 00:00:00';
        $to_date  = $request->date . ' 23:59:59';

        $trip = Trip::with('pickupLocation', 'dropLocation')->where('driver_id', $request->driver_id)
          ->whereBetween('clock_out_time', [$from_date, $to_date])
          ->where('status', 3)
          ->get();

        if (count($trip) < 1) {
          $response['success'] = false;
          $response['message'] = "No completed Trip found for the selected date.";
        } else {

          $response['success'] = true;
          $response['message'] = "Success! Completed trip found.";

          foreach ($trip as $key => $value) {
            $temp['trip_id']         = $value->id;
            if ($value->pickupLocation) {
              $temp['pickup_location']       = $value->pickupLocation->location_name ?? $value->pickupLocation->location;
            }
            if ($value->dropLocation) {
              $temp['drop_location']       = $value->dropLocation->location;
            }
            $temp['total_cost']       = $value->total_cost;
            $temp['client_name']       = $value->client_name;
            $temp['clock_in_time']       = $value->clock_in_time;
            $temp['clock_out_time']       = $value->clock_out_time;

            $response['data'][] = $temp;
          }
        }
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }
  public function all_completed_trip(Request $request)
  {
    $driver = Driver::where('id', $request->driver_id)->first();
    //  return $driver;
    if (empty($driver)) {
      $response['success'] = false;
      $response['message'] = "No completed Trip found for the selected date.";
    } else {
      $trip = Trip::with('pickupLocation', 'dropLocation')->where('driver_id', $request->driver_id)
        ->whereNotNull('clock_out_time')
        ->where('status', 3)
        ->get();

      if (count($trip) < 1) {
        $response['success'] = false;
        $response['message'] = "No completed Trip found for the selected date.";
      } else {

        $response['success'] = true;
        $response['message'] = "Success! Completed trip found.";

        foreach ($trip as $key => $value) {
          $temp['trip_id']         = $value->id;
          if ($value->pickupLocation) {
            $temp['pickup_location']       = $value->pickupLocation->location;
          } else {
            $temp['pickup_location'] = '';
          }
          if ($value->dropLocation) {
            $temp['drop_location']       = $value->dropLocation->location;
          } else {
            $temp['drop_location']       = '';
          }
          $temp['total_cost']       = $value->total_cost;
          $temp['client_name']       = $value->client_name;
          $temp['clock_in_time']       = $value->clock_in_time;
          $temp['clock_out_time']       = $value->clock_out_time;

          $response['data'][] = $temp;
        }
      }
    }


    return json_encode($response);
  }

  //function used to see the driver trip commission earn according to date
  public function driver_trip_commission(Request $request)
  {
    if ($request->driver_id && $request->date) {
      $driver = Driver::where('id', $request->driver_id)->first();

      if (empty($driver)) {
        $response['success'] = false;
        $response['message'] = "No commission found for the selected date.";
      } else {

        $from_date = $request->date . ' 00:00:00';
        $to_date  = $request->date . ' 23:59:59';

        $trip = Trip::with('pickupLocation', 'dropLocation')->where('driver_id', $request->driver_id)
          ->whereBetween('clock_out_time', [$from_date, $to_date])
          ->where('status', 3)
          ->get();

        if (count($trip) < 1) {
          $response['success'] = false;
          $response['message'] = "No commission found for the selected date.";
        } else {

          $response['success'] = true;
          $response['message'] = "Success! Commission found.";

          $revenue = 0;
          foreach ($trip as $key => $value) {
            $revenue += $value->driver_commission;
            $temp['trip_id']         = $value->id;
            if ($value->pickupLocation) {
              $temp['pickup_location']       = $value->pickupLocation->location_name;
            }
            if ($value->dropLocation) {
              $temp['drop_location']       = $value->dropLocation->location;
            }
            $temp['driver_commission']       = $value->driver_commission;
            $temp['clock_in_time']       = $value->clock_in_time;
            $temp['clock_out_time']       = $value->clock_out_time;

            $response['data'][] = $temp;
          }
          $response['total_commission']   =   $revenue;
        }
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }


  public function all_commission_trip(Request $request)
  {
    $driver = Driver::where('id', $request->driver_id)->first();

    if (empty($driver)) {
      $response['success'] = false;
      $response['message'] = "No commission found for the selected date.";
    } else {

      $from_date = $request->date . ' 00:00:00';
      $to_date  = $request->date . ' 23:59:59';

      $trip = Trip::with('pickupLocation', 'dropLocation')->where('driver_id', $request->driver_id)->whereNotNull('clock_out_time')->where('status', 3)->get();

      if (count($trip) < 1) {
        $response['success'] = false;
        $response['message'] = "No commission found for the selected date.";
      } else {

        $response['success'] = true;
        $response['message'] = "Success! Commission found.";

        foreach ($trip as $key => $value) {
          $temp['trip_id']         = $value->id;
          if ($value->pickupLocation) {
            $temp['pickup_location']       = $value->pickupLocation->location;
          }
          if ($value->dropLocation) {
            $temp['drop_location']       = $value->dropLocation->location;
          }
          $temp['driver_commission']       = $value->driver_commission;
          $temp['clock_in_time']       = $value->clock_in_time;
          $temp['clock_out_time']       = $value->clock_out_time;
          $response['data'][] = $temp;
        }
      }
    }

    return json_encode($response);
  }

  //function used to show the driver running trip
  public function driver_running_trip(Request $request)
  {
    if ($request->trip_id) {
      $trip = Trip::with('dropLocation', 'pickupLocation')
        ->where('id', $request->trip_id)
        ->orderBy('id', 'desc')
        ->whereIn('status', [2, 4])
        ->first();
      if (!$trip) {
        $response['success'] = false;
        $response['message'] = "Error! Trip not found.";
      } else {
        if ($trip->status == 4) {

          if (!empty($trip->is_canceled_seen_by_driver)) {
            $temp['is_driver_seen_cancel'] = true;
          } else {
            $temp['is_cancelled'] = true;
            Trip::where('id', $trip->id)->update(['is_canceled_seen_by_driver' => 1]);
          }
        }
        $temp['trip_id']  = $trip->id;
        if ($trip->pickupLocation) {
          $temp['pickup_location']  = $trip->pickupLocation->location;
          $temp['pickup_location_name']  = $trip->pickupLocation->location_name ?? $trip->pickupLocation->location;
          $temp['pic_location_lat']  = $trip->pickupLocation->latitude;
          $temp['pic_location_long']  = $trip->pickupLocation->longitude;
        }
        if ($trip->dropLocation) {
          $temp['drop_location']  = $trip->dropLocation->location;
          $temp['drop_location_lat']  = $trip->dropLocation->latitude;
          $temp['drop_location_long']  = $trip->dropLocation->longitude;
        }
        $temp['total_cost']  = $trip->total_cost;
        $temp['client_name']  = $trip->client_name;
        $temp['total_time']  = $trip->total_time;
        $temp['clock_in_time']  = $trip->clock_in_time;
        $response['success'] = true;
        $response['message'] = "Success! Trip found";

        $response['data'] = $temp;
        return $response;
      }
    } else {

      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }
    return json_encode($response);
  }

  //function used to track driver last active
  public function driver_last_active(Request $request)
  {
    if ($request->user_id) {
      $user = User::where('id', $request->user_id)->first();

      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! User not found.";
      } else {

        $user->update(['last_active' => now()]);

        $response['success'] = true;
        $response['message'] = "Success! Driver last active updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  public function running_trip(Request $request)
  {
    $driver_id = $request->driver_id;
    $trip = Trip::where('driver_id', $driver_id)->where('status', 2)->whereNotNull('clock_in_time')->whereNull('clock_out_time')->first();
    return $trip;
  }
  public function trip(Request $request)
  {
    $trip_id = $request->trip_id;
    $trip = Trip::with('dropLocation', 'pickupLocation')->where('id', $trip_id)->first();
    return $trip;
  }

  public function update_trip(Request $request)
  {
    $trip_id = $request->trip_id;
    $trip = Trip::find($trip_id);
    $trip->total_time = $request->total_time;
    $trip->save();
    return $trip;
  }

  public function pic_location(Request $request)
  {
    $pic_location = new PickupLocation();
    $pic_location->location = $request->location;
    $pic_location->latitude = $request->lat;
    $pic_location->longitude = $request->lon;
    $pic_location->save();
    return $pic_location->id;
  }

  public function pendingTrip(Request $request)
  {
    $driver_id = $request->driver_id;
    $trips = Trip::where(['driver_id' => $driver_id, 'status' => 1])->whereNotNull('client_id')->orderBy('pick_up_datetime', 'asc')->get()->each(function ($item) {
      $item->pickup_location = $item->pickup_location_alias();
      $item->drop_location = $item->drop_location();
      $item->client = $item->client_info();
    });
    return $trips;
  }

  public function singleTrip(Request $request)
  {
    $driverId = $request->driver_id;
    $tripId = $request->trip_id;
    $trip = Trip::where(['id' => $tripId, 'driver_id' => $driverId])->first();
    if ($trip) {
      $statusText = [1 => 'New', 2 => 'Running', 3 => 'Completed', 4 => 'Canceled', 5 => 'Declined'];  // status indications
      $trip->drop_location = $trip->drop_location();  // append drop-off location name 
      $trip->pickup_location = $trip->pickup_location_alias();  // append pickup location name 

      $trip->status = $statusText[$trip->status];
      $trip->merchant = $trip->merchant_info();
      $trip->driver = $trip->driver_info();
      $trip->client = $trip->client_info();
      $trip->truck_number = $trip->truck_number();
      $trip->payment = $trip->payment();
      $trip->request_status = $trip->request_status();
    }

    return $trip;
  }

  public function driverStats(Request $request)
  {
    $driverId = $request->driver_id;

    $pending_trips = Trip::whereYear('created_at', date('Y'))->where(['driver_id' => $driverId, 'status' => 1])->whereNotNull('client_id')->count();
    $completed_trips = Trip::whereYear('created_at', date('Y'))->where(['driver_id' => $driverId, 'status' => 3])->whereNotNull('client_id')->count();
    $notifications = TripRequest::where(['driver_id' => $driverId])->count();
    $expenses = Expense::whereYear('created_at', date('Y'))->where(['driver_id' => $driverId])->count();

    $data = [
      "pending_trips" => $pending_trips,
      "completed_trips" => $completed_trips,
      "notifications" => $notifications,
      "expenses" => $expenses,
    ];
    return $data;
  }

  public function tripRequests(Request $request)
  {
    $driverId = $request->driver_id;
    $notifications = TripRequest::where(['driver_id' => $driverId])->whereNotNull('client_id')->get()->each(function ($item) {
      $item->trip = $item->trip();
      $item->client = $item->client();
    });
    return $notifications;
  }

  public function tripRequestsDelete(Request $request, $id)
  {
    $driverId = $request->driver_id;
    $notifications = TripRequest::where(['id' => $id, 'driver_id' => $driverId])->first();
    $trip = Trip::where('id', $notifications->trip_id)->first();
    if ($trip) {
      $trip->status = 5;
      $trip->save();
    }

    if ($notifications) {
      $notifications->delete();
    }
    return $notifications;
  }

  public function driverTripDecline(Request $request, $id)
  {
    $driverId = $request->driver_id;
    $notifications = TripRequest::where(['trip_id' => $id, 'driver_id' => $driverId])->first();
    $trip = Trip::where('id', $id)->first();
    if ($trip) {
      $trip->status = 5;
      $trip->save();
    }

    if ($notifications) {
      $notifications->status = 3;
      $notifications->save();
      return $notifications->delete();
    }
    return false;
  }

  public function driverTripAccept(Request $request, $id)
  {
    $driverId = $request->driver_id;
    $notifications = TripRequest::where(['trip_id' => $id, 'driver_id' => $driverId])->first();

    if ($notifications) {
      $notifications->status = 2;
      $notifications->save();
    }

    return $notifications;
  }

  public function tripRequestsReadAll(Request $request)
  {
    $driverId = $request->driver_id;
    $notifications = TripRequest::where(['driver_id' => $driverId])->update(['is_read' => 1]);
    return $notifications;
  }

  public function driverDeviceToken(Request $request)
  {
    $driverId = $request->driver_id;
    // save driver device token in database to send the notification on logged in device
    Driver::where(['id' => $driverId])->update(['device_token' => $request->device_token]);
    return response()->json(['device_token' => $request->device_token]);
  }
}
