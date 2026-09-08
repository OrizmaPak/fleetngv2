<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Driver;
use App\Models\FrontModel;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class TripController extends Controller
{
  private $api_url;
  public function __construct()
  {
    $this->api_url = config('app.url') . 'api/';
  }

  //function used to show trip list
  public function trip_list()
  {
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],
      ['name' => "Trip List"]
    ];
    return view('/admin/trip-management/trip-list', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  // this function is used for ajax call to show trip list detail
  public function trip_list_detail(Request $request)
  {
    if (session('user_role') == 1) {
      $driver_id = Driver::pluck('id');
    } else {
      $driver_id =  Driver::where('user_id', Auth::user()->id)->pluck('id');
    }
    if ($request->ajax()) {
      $data = Trip::with('driver', 'pickupLocation', 'dropLocation')
        ->whereIn('driver_id', $driver_id)
        ->orderBy('id', 'DESC')
        ->get();
      return Datatables::of($data)
        ->addIndexColumn()
        ->addColumn('name', function ($data) {
          return '<a href="' . route('view-driver', $data->driver->id) . '" class="mr-1">' . $data->driver->first_name . ' ' . $data->driver->last_name . '</a>';
        })
        ->addColumn('client_name', function ($data) {
          return  $data->client_name;
        })
        ->addColumn('total_cost', function ($data) {
          return number_format($data->total_trip_cost(), 2);
        })
        ->addColumn('driver_commission', function ($data) {

          return number_format($data->driver_commission, 2);
        })
        ->addColumn('trip_generated_at', function ($data) {

          return date("d/m/Y", strtotime($data->trip_generated_at));
        })
        ->addColumn('pickup', function ($data) {

          if (isset($data->pickupLocation->location)) {
            return $data->pickupLocation->location;
          } else {
            return '';
          }
        })

        ->addColumn('drop_off', function ($data) {
          return $data->dropLocation->location;
        })

        ->addColumn('status', function ($data) {

          if ($data->status == 1) {
            return 'New Trip';
          } else if ($data->status == 2) {
            return '<a href="' . route('trip-live-location', $data->id) . '"  class="badge badge-success"><u>Live Trip</u></a>';
          } else if ($data->status == 3) {
            return 'Completed Trip';
          } else {
            return 'Canceled Trip';
          }
        })
        ->addColumn('action', function ($data) {
          if ($data->status == 1 || $data->status == 2) {
            $cancel_trip = '<a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
          } else {
            $cancel_trip = '';
          }
          $btn = '<a href="' . route('trip-detail', $data->id) . '" class="mr-1"><i class="fas fa-eye"></i></a>' . $cancel_trip;
          return $btn;
        })
        ->rawColumns(['name', 'client_name', 'action', 'pickup', 'drop_off', 'trip_generated_at', 'status'])
        ->make(true);
    }
  }

  // this function is used for ajax call to show trip list detail according to filter
  public function trip_list_detail_filter(Request $request)
  {
    $trip_type = $request->trip_type;
    $driver = $request->search_driver;
    $location = $request->search_location;
    $daterange = $request->daterange;
    $alltrips = ApiModel::fetchAllTrips($trip_type, $driver, $location, $daterange);
    return Datatables::of($alltrips)
      ->addIndexColumn()
      ->addColumn('name', function ($data) {
        return '<a href="' . route('view-driver', $data->driver_id) . '" class="mr-1">' . $data->first_name . ' ' . $data->last_name . '</a>';
      })
      ->addColumn('client_name', function ($data) {
        return  $data->client_name;
      })
      ->addColumn('total_cost', function ($data) {
        $total_trip_cost = $data->total_cost + ($data->cost_of_sand ?? 0) + ($data->road_money ?? 0);
        return number_format($total_trip_cost, 2);
      })
      ->addColumn('driver_commission', function ($data) {
        return number_format($data->driver_commission, 2);
      })
      ->addColumn('trip_generated_at', function ($data) {
        return date("d/m/Y", strtotime($data->trip_generated_at));
      })
      ->addColumn('pickup', function ($data) {
        return $data->pickup_location;
      })
      ->addColumn('drop_off', function ($data) {
        return $data->drop_location;
      })
      ->addColumn('status', function ($data) {
        if ($data->status == 1) {
          return 'New Trip';
        } else if ($data->status == 2) {
          return '<a href="' . route('trip-live-location', $data->id) . '"  class="badge badge-success"><u>Live Trip</u></a>';
        } else if ($data->status == 3) {
          return 'Completed Trip';
        } else {
          return 'Canceled Trip';
        }
      })
      ->addColumn('action', function ($data) {
        if ($data->status != 4) {
          $cancel_trip = '<a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
        } else {
          $cancel_trip = '';
        }
        $btn = '<a href="' . route('trip-detail', $data->id) . '" class="mr-1"><i class="fas fa-eye"></i></a>' . $cancel_trip;
        return $btn;
      })
      ->rawColumns(['name', 'action', 'pickup', 'drop_off', 'trip_generated_at', 'status'])
      ->make(true);
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

  //function used to view trip details
  public function trip_detail($id)
  {
    $url = $this->api_url . "get-trip-detail";
    $params          = array('trip_id' => $id);
    $trip_details = FrontModel::callPostCurl($url, $params);
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],
      ['link' => "/trip-list", 'name' => "Trip Management"],
      ['name' => "Trip Detail"]
    ];
    return view('/admin/trip-management/trip-detail', [
      'breadcrumbs' => $breadcrumbs,
      'trip_details' => $trip_details['data']
    ]);
  }
  public function trip_live_location($id)
  {
    return view('/admin/trip-management/trip-live-location');;
  }
}
