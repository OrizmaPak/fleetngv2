<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Actions\GetTripsForAjaxDatatableAction;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\DropLocation;
use App\Models\FrontModel;
use App\Models\PickupLocation;
use App\Models\Trip;
use App\Models\TripPayment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TripController extends Controller
{
  private $api_url;
  private $allowUserType;
  public function __construct()
  {
    $this->api_url = rtrim(config('app.url'), '/') . '/api/';
    $this->allowUserType = config('custom.permission');
  }


  //function used to show trip list
  public function trip_list()
  {
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],  ['name' => "Trip List"]
    ];

    $merchant_name = "";

    if (Auth::user()->merchant_assigned) {

      $merchant = User::where('id', Auth::user()->merchant_assigned)->first();

      if ($merchant && $merchant->merchant_name) {
        $merchant_name = $merchant->merchant_name;
      }
    }

    return view('/admin/trip-management/trip-list', [
      'breadcrumbs' => $breadcrumbs,
      'merchant_name' => $merchant_name
    ]);
  }
  
  public function trip_confirm_pos($id)
  {
    return $this->recordPayment([$id], 'confirmed_pos');
  }

  public function trip_bank_payment_received($id)
  {
    return $this->recordPayment([$id], 'confirmed_bank_transfer');
  }

  public function bulk_trip_bank_payment_received(Request $request)
  {
    return $this->recordPayment(explode(',', (string) $request->transfer_trip_ids), 'confirmed_bank_transfer');
  }

  public function bulk_trip_pos_payment_received(Request $request)
  {
    return $this->recordPayment(explode(',', (string) $request->pos_trip_ids), 'confirmed_pos');
  }

  private function recordPayment(array $ids, $method)
  {
    app(\App\Services\ManualTripPayment::class)->record(Auth::user(), $ids, $method);
    return redirect(route('trip-list'))->with('success', 'Trip payment status updated successfully.');
  }

  // this function is used for ajax call to show trip list detail
  public function trip_list_detail(Request $request)
  {

    abort_if(!$request->ajax(), 400);

    $user = Auth::user();
    if (in_array(session('user_role'), $this->allowUserType)) { //superadmin
      $driver_id = Driver::pluck('id');
    } else {
      if ($user->user_type == 2) { //admin
        $driver_id =  Driver::pluck('id');
      } else if ($user->is_payment_user && $user->merchant_assigned) {
        $driver_id =  Driver::where('merchant_id', $user->merchant_assigned)->pluck('id'); // to show all merchants trips 
      } else {
        $driver_id =  Driver::where('user_id', $user->id)->pluck('id');
      }
    }

    $statusCode = [1, 2, 3, 4, 5]; // UPDATE ON 15 JUNE 2024 - Need to show new trips for user so he can have ability to delete a mistake made at trip creation level

    $recordPerPage = (int) $request->input('length', 10);
    $start = (int) $request->input('start');
    $page = $start / $recordPerPage;
    $page = $page < 1 ? 1 : $page;

    $query = Trip::with('driver', 'client', 'pickupLocation', 'dropLocation')
      ->whereIn('driver_id', $driver_id)
      ->whereIn('status', $statusCode)
      ->whereNull('trips.deleted_at') //exclude deleted at records
      ->orderBy('id', $request->input('order.0.dir', 'desc'));

    $totalRecords = $query->count();

    $trips = $query->clone()
      ->take($recordPerPage)
      ->skip($start)
      ->get();

    return GetTripsForAjaxDatatableAction::getResponse(
      $trips,
      $user,
      $page,
      $totalRecords,
      $totalRecords,
      $request->input('order.0.dir', 'desc'),
      $recordPerPage
    );
  }

  // this function is used for ajax call to show trip list detail according to filter
  public function trip_list_detail_filter(Request $request)
  {

    $trip_type = $request->trip_type;
    $driver = $request->search_driver;
    $client = $request->search_client;
    $location = $request->search_location;
    $daterange = $request->daterange;
    $user = Auth::user();

    $created_at = '';
    $from_date = '';
    $to_date = '';

    if (!empty($daterange)) {
      $date = explode('to', $daterange);
      if (count($date) == 1) {
        $created_at = Carbon::parse($date[0])->toDateTimeString();
        $from_date = '';
        $to_date = '';
      } else {
        $created_at = '';
        $from_date = Carbon::parse($date[0])->toDateTimeString();
        $to_date = Carbon::parse($date[1])->setTime(23, 59, 59)->toDateTimeString();
      }
    }

    if (session('user_role') == 1) {
      $driver_id = Driver::pluck('id');
    } else {
      if ($user->user_type == 2) { //admin
        $driver_id =  Driver::pluck('id');
      } else { //merchant and user
        $driver_id =  Driver::where('user_id', $user->id)->pluck('id');
        if ($user->is_payment_user && $user->merchant_assigned) {
          $driver_id =  Driver::where('merchant_id', $user->merchant_assigned)->pluck('id'); //to show all merchants trip 
        } else {
          $driver_id =  Driver::where('user_id', $user->id)->pluck('id');
        }
      }
    }

    $recordPerPage = (int) $request->input('length', 10);
    $start = (int) $request->input('start');
    $page = $start / $recordPerPage;
    $page = $page < 1 ? 1 : $page;

    $statusCode = [1, 2, 3, 4, 5]; // UPDATE ON 15 JUNE 2024 - Need to show new trips for user so he can have ability to delete a mistake made at trip creation level

    $query = Trip::with('driver', 'client', 'pickupLocation', 'dropLocation')
      ->whereIn('driver_id', $driver_id)
      ->whereIn('status', $statusCode)
      ->whereNull('trips.deleted_at');

    $totalRecords = $query->count();

    $filterQuery = $query->clone()
      ->where(function ($query) use ($trip_type) {
        if ($trip_type != 'all') {
          $query->where('trips.status', $trip_type);
        }
      })
      ->where(function ($subQuery) use ($driver) {
        if ($driver != '') {
          $subQuery->whereHas('driver', function ($builder) use ($driver) {
            $builder->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $driver . '%']);
          });
        }
      })
      ->where(function ($subQuery) use ($client) {
        if ($client != '') {
          $subQuery->where('client_name', 'like', '%' . $client . '%');
        }
      })
      ->where(function ($subQuery) use ($location) {
        if ($location != '') {
          $subQuery->whereHas('pickuplocation', function ($builder) use ($location) {

            $builder->where(function ($builderSubQuery) use ($location) {
              $builderSubQuery->where('pickup_locations.location', 'like', '%' . $location . '%')
                ->orWhere('pickup_locations.location_name', 'like', '%' . $location . '%');
            });
          })
            ->orwhereHas('droplocation', function ($builder) use ($location) {
              $builder->where('drop_locations.location', 'like', '%' . $location . '%');
            });
        }
      })
      ->where(function ($subQuery) use ($created_at) {
        if ($created_at != '') {
          $subQuery->whereDate('trips.created_at', '=', $created_at);
        }
      })
      ->where(function ($subQuery) use ($from_date) {
        if ($from_date != '') {
          $subQuery->where('trips.created_at', '>=', $from_date);
        }
      })
      ->where(function ($subQuery) use ($to_date) {
        if ($to_date != '') {
          $subQuery->where('trips.created_at', '<=', $to_date);
        }
      });

    $totalFilterRecords = $filterQuery->count();

    $filterTrips = $filterQuery->clone()
      ->orderBy('id', $request->input('order.0.dir', 'desc'))
      ->take($recordPerPage)
      ->skip($start)
      ->get();

    return GetTripsForAjaxDatatableAction::getResponse(
      $filterTrips,
      $user,
      $page,
      $totalRecords,
      $totalFilterRecords,
      $request->input('order.0.dir', 'desc'),
      $recordPerPage,
      true
    );
  }


  //function used to add driver details
  public function trip_add(Request $request)
  {
    if ($request->isMethod('post')) {
      $request->validate(['client_type'=>'required|in:new,existing','client_id'=>'required_if:client_type,existing|nullable|integer|exists:customers,id','client_name'=>'required_if:client_type,new|nullable|string|max:255','client_phone'=>'required_if:client_type,new|nullable|regex:/^[0-9]{9,13}$/','client_email'=>'nullable|email','driver_id'=>'required|integer|exists:drivers,id','pickup_location'=>'required|integer|exists:pickup_locations,id','drop_location'=>'required|string|max:255','pickup_datetime'=>'required|date|after_or_equal:today','trip_cost'=>'required|integer|min:0','cost_of_sand'=>'nullable|integer|min:0','road_money'=>'nullable|integer|min:0']);
      abort_unless(\App\Support\StaffAccess::drivers(Auth::user())->whereKey($request->driver_id)->exists(),404);
      if ($request->client_type === 'existing' && !\App\Support\StaffAccess::global(Auth::user())) abort_unless(in_array((int)$request->client_id, Auth::user()->client_ids()->map(function($id){return (int)$id;})->all(),true),404);
      $trip = \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
      $client_phone = $request->client_phone;
      $client_email = $request->client_email;
      $client_name = $request->client_name;
      $client = Customer::where('phone_number', $client_phone)->first();

      if ($request->client_type === 'existing' && $request->client_id) {
        $client = Customer::where('id', $request->client_id)->first();
        $client_name = $client->full_name;
      } else {
        if (!$client) {
          $client = Customer::create(['first_name' => $client_name, 'email' => $client_email, 'phone_number' => $client_phone, 'country_code' => $request->country_code]);
        }
      }

      if (!$client) {
        Session::flash('fail', 'Something went wrong.');
        return redirect()->back()->withInputs();
      }

      $drop_location_id = DropLocation::create(['location' => $request->drop_location])->id; // insert drop-off location in database

      $data = [
        'driver_id' => $request->driver_id,
        'client_id' => $client->id,
        'pickup_location_id' => $request->pickup_location,
        'drop_location_id' => $drop_location_id,
        'trip_generated_at' => Carbon::now(),
        'pick_up_datetime' => $request->pickup_datetime,
        'client_name' => $client_name,
        'total_cost' => $request->trip_cost,
        'user_id' => Auth::user()->id
      ];

      if($request->cost_of_sand){
        $data['cost_of_sand'] = $request->cost_of_sand;
      }

      if($request->road_money){
        $data['road_money'] = $request->road_money;
      }

      $trip = Trip::create($data); // create trip in database
      return $trip;
      });

      try {
        $trip->send_trip_request_notification();
      } catch (\Throwable $error) {
        report($error);
        return redirect(route('trip-list'))->with('fail', 'Trip saved, but the driver notification failed. Do not create another trip.');
      }

      // Add trip request for driver
      if ($trip) {
        return redirect(route('trip-list'))->with('success', 'Trip added successfully.');
      } else {
        Session::flash('fail', 'Something went wrong.');
        return redirect()->back()->withInputs();
      }
    } else {

      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "/trip-list", 'name' => "Trip Management"],  ['name' => "Add Trip"]
      ];

      $drivers = Auth::user()->merchant_drivers();
      $clients = \App\Support\StaffAccess::global(Auth::user()) ? Customer::get() : Customer::whereIn('id', Auth::user()->client_ids())->get();

      $locations = PickupLocation::where('is_active', 1)->get();

      return view('/admin/trip-management/trip-add', [
        'breadcrumbs' => $breadcrumbs,
        'drivers' => $drivers,
        'clients' => $clients,
        'locations' => $locations
      ]);
    }
  }

  //function used to cancel trip 
  public function trip_cancel(Request $request)
  {
    $url = $this->api_url . "cancel-trip";
    $params          = array('trip_id' => $request->trip_id);
    $trip_delete = FrontModel::callPostCurl($url, $params);

    if ($trip_delete['success']) {
      return redirect(route('trip-list'))->with('success', $trip_delete['message']);
    } else {
      return redirect(route('trip-list'))->with('fail', $trip_delete['message']);
    }
  }

  //function used to delete trip 
  public function trip_delete(Request $request)
  {
    $url = $this->api_url . "delete-trip";
    $params          = array('trip_id' => $request->trip_id);
    $trip_delete = FrontModel::callPostCurl($url, $params);
    if ($trip_delete['success']) {
      return redirect(route('trip-list'))->with('success', $trip_delete['message']);
    } else {
      return redirect(route('trip-list'))->with('fail', $trip_delete['message']);
    }
  }
  //function used to delete trip 
  public function change_trip_driver(Request $request)
  {
    $url = $this->api_url . "change-trip-driver";
    $params          = ['trip_id' => $request->trip_id, 'driver_id' => $request->driver_id];
    $trip_delete = FrontModel::callPostCurl($url, $params);
    if ($trip_delete['success']) {
      return redirect(route('trip-list'))->with('success', $trip_delete['message']);
    } else {
      return redirect(route('trip-list'))->with('fail', $trip_delete['message']);
    }
  }

  //function used to view trip details
  public function trip_detail($id)
  {
    $url = $this->api_url . "get-trip-detail";
    $params          = array('trip_id' => $id);
    $trip_details = FrontModel::callPostCurl($url, $params);

    $user_id = Auth::user()->id;
    $user_type = Auth::user()->user_type;
    $drivers = null;

    if ($user_type == 3) { // user
      $drivers = Driver::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
    } else { //admin and superadmin
      $drivers = Driver::orderBy('id', 'DESC')->get();
    }

    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"], ['link' => "/trip-list", 'name' => "Trip Management"],  ['name' => "Trip Detail"]
    ];
    return view('/admin/trip-management/trip-detail', [
      'breadcrumbs' => $breadcrumbs,
      'trip_details' => $trip_details['data'],
      'drivers' => $drivers,
    ]);
  }
  public function trip_live_location(Request $request, $id)
  {
    if (!$request->ajax()) {
      $trip = Trip::where('trips.id', $id)->with('droplocation', 'pickuplocation')
        ->join('drivers', 'drivers.id', '=', 'trips.driver_id')
        ->first();
      if (empty($trip)) {
        return redirect(url('trip-list'));
      }
      $serial =  $trip->device_serial_number;
    } else {
      $serial = $request->serial;
    }
    $live_tracking_url = "https://app.zypsa.com/tracking_api.php";
    $live_tracking_params          = [
      'user_name' => config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "track"
    ];
    $live_tracking_distance_params          = [
      'user_name' => config('integrations.tracking_username'),
      'hash_key' => config('integrations.tracking_key'),
      "action" => "extra"
    ];
    $live_tracking_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_params));
    $live_tracking_distance_data = FrontModel::callPostCurl($live_tracking_url, json_encode($live_tracking_distance_params));
    $live_tracking_details = $live_tracking_data['data']['vehicles'];
    $live_tracking_distance_details = $live_tracking_distance_data['data']['vehicle_data'];
    $data = array_replace_recursive($live_tracking_details, $live_tracking_distance_details);
    if ($serial) {
      $vehicle_key = array_search($serial, array_column($data, 'serial'));
    }
    $vehicle_details = $data[$vehicle_key];
    if ($request->ajax()) {
      if (!empty($vehicle_details)) {
        $message = 'Data Found';
      } else {
        $message = 'No record found';
      }
      return [
        'message'         => $message,
        'vehicle_details' => $vehicle_details
      ];
    }
    if (empty($vehicle_details)) {
      return redirect(url('trip-list'));
    }

    return view('/admin/trip-management/trip-live-location', compact('vehicle_details', 'trip'));
  }
}
