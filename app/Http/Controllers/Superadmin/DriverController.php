<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Driver;
use App\Models\FrontModel;
use App\Models\Trip;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
  private $api_url;
  public function __construct()
  {
    $this->url = config('app.url');
    $this->api_url = $this->url . 'api/';
  }


  //function used to show driver list
  public function driver_list(Request $request)
  {


    if(session('user_role') == 1){
      $driver_id = null;
      if($request->driver){
        $driver_id = base64_decode($request->driver);
      }else{
        $driver_id = null;
      }
    }else{
      $driver_id = Auth::user()->id;
    }
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],  ['name' => "Driver List"]
    ];
    return view('/admin/driver-management/driver-list', [
      'breadcrumbs' => $breadcrumbs , 'user_id'=>$driver_id
    ]);
  }

 
  // this function is used for ajax call to show driver list detail
  public function driver_list_detail(Request $request)
  {
    $user_id = $request->user_id;
    if ($request->ajax()) {
      if($user_id){
        $data = Driver::where('user_id', $user_id)->orderBy('id', 'DESC')->get();
      }else{
        $data = Driver::orderBy('id', 'DESC')->get();
      }
      return Datatables::of($data)
        ->addIndexColumn()

        ->addColumn('is_active', function ($data) {
          if ($data->is_active == 1) {
            $btn = '<div class="badge badge-success">Active</div>';
          } else {
            $btn = '<div class="badge badge-danger">Inactive</div>';
          }
          return $btn;
        })

        ->addColumn('name', function ($data) {

          return '<a href="' . route('view-driver', $data->id) . '" class="mr-1">'.$data->first_name . ' ' . $data->last_name.'</a>';
        })
        ->addColumn('username', function ($data) {
          $user = User::select('first_name', 'last_name')->where('id', $data->user_id)->first();
          if(session('user_role') == 1){
            return '<a href="' . route('view-user', $data->user_id) . '" class="mr-1">'.$user->first_name . ' ' . $user->last_name.'</a>';
          }else{
            return $user->first_name.' '.$user->last_name;
          }
        })
        ->addColumn('vehicle_id', function ($data) {

          return $data->vehicle_id;
        })

        ->addColumn('total_trips', function ($data) {

          $trip = Trip::where('driver_id', $data->id)->count();

          if ($trip > 0) {
            return '<a href="' . route("driver-trip-list", $data->id) . '" style="text-decoration: underline;">' . $trip . '</a>';
          } else {
            return 'No Trip';
          }
        })

        ->addColumn('auth_pin', function ($data) {


          $login = '<input type="password" maxlength="4" onkeypress="return onlyNumberKey(event)"  class="form-control" id="login_pin_input'.$data->id.'" value="'.$data->auth_pin.'" style="width: 40%;" onblur="save_pin('.$data->id.','.$data->auth_pin.')" />';
          return $login;
        })

        ->addColumn('last_active', function ($data) {
          if($data->last_active)
          {
            return Carbon::parse($data->last_active)->diffForHumans();
          }
          else
          {
            return 'Not Logged in yet';
          }
          
        })

        ->addColumn('action', function ($data) {
          $base_url = url('/');

          if ($data->is_active == 1) {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',0)"><i class="fas fa-unlock"></i></a>';
          } else {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',1)"><i class="fas fa-lock"></i></a>';
          }

          $btn = '<a href="' . route('edit-driver', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
                  <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
          return $btn;
        })

        ->rawColumns(['name', 'is_active', 'username', 'total_trips','auth_pin', 'last_active', 'action'])
        ->make(true);
    }
  }

  // this function is used for ajax call to show trip list detail according to filter
  public function driver_list_detail_filter(Request $request)
  {

    $driver_type = $request->driver_type;
    $driver = $request->search_driver;
    $vehicle = $request->search_vehicle;
    $company_user = $request->company_user;

    // $daterange;

    $data = ApiModel::fetchAllDrivers($driver_type, $driver, $vehicle , $company_user);
    return Datatables::of($data)
    ->addIndexColumn()

    ->addColumn('is_active', function ($data) {
      if ($data->is_active == 1) {
        $btn = '<div class="badge badge-success">Active</div>';
      } else {
        $btn = '<div class="badge badge-danger">Inactive</div>';
      }
      return $btn;
    })

    ->addColumn('name', function ($data) {

      return '<a href="' . route('view-driver', $data->id) . '" class="mr-1">'.$data->first_name . ' ' . $data->last_name.'</a>';
    })

    ->addColumn('total_trips', function ($data) {

      $trip = Trip::where('driver_id', $data->id)->count();

      if ($trip > 0) {
        return '<a href="' . route("driver-trip-list", $data->id) . '" style="text-decoration: underline;">' . $trip . '</a>';
      } else {
        return 'No Trip';
      }
    })
    ->addColumn('username', function ($data) {
      $user = User::select('first_name', 'last_name')->where('id', $data->user_id)->first();
      if(session('user_role') == 1){
        return '<a href="' . route('view-user', $data->user_id) . '" class="mr-1">'.$user->first_name . ' ' . $user->last_name.'</a>';
      }else{
        return $user->first_name.' '.$user->last_name;
      }
    })

    ->addColumn('auth_pin', function ($data) {
      $login = '<input type="password" maxlength="4" onkeypress="return onlyNumberKey(event)"  class="form-control" id="login_pin_input'.$data->id.'" value="'.$data->auth_pin.'" style="width: 40%;" onblur="save_pin('.$data->id.','.$data->auth_pin.')" />';
      return $login;
    })

    ->addColumn('last_active', function ($data) {
      if($data->last_active)
      {
        return Carbon::parse($data->last_active)->diffForHumans();
      }
      else
      {
        return 'Not Logged in yet';
      }
      
    })

    ->addColumn('action', function ($data) {
      $base_url = url('/');

      if ($data->is_active == 1) {
        $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',0)"><i class="fas fa-unlock"></i></a>';
      } else {
        $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',1)"><i class="fas fa-lock"></i></a>';
      }

      $btn = '<a href="' . route('edit-driver', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
              <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
      return $btn;
    })

    ->rawColumns(['name', 'is_active', 'username', 'total_trips','auth_pin', 'last_active', 'action'])
    ->make(true);
  }

  //function used to add driver details
  public function add_driver(Request $request)
  {

    if ($request->isMethod('post')) {

      $url = $this->api_url . "add-driver";

      $params          = [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'photo' => $request->crop_image_id,
        'vehicle_id' => $request->vehicle_id,
        'login_pin'=>$request->login_pin,
        'user_id'=>Auth::user()->id
      ];

      $add_driver_data = FrontModel::callPostCurl($url, $params);
      if ($add_driver_data['success']) {
        return redirect(route('driver-list'))->with('success', $add_driver_data['message'].' Driver Login Pin is '.$add_driver_data['login_pin']);
      } else {
        Session::flash('fail', $add_driver_data['message']);
        return redirect(route('add-driver'))->withInput();
      }
    } else {
      
      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => "Driver Management"],  ['name' => "Add Driver"]
      ];
      
      return view('/admin/driver-management/add-driver', [
        'breadcrumbs' => $breadcrumbs
      ]);
    }
  }

  //function used to change driver login pin
  public function change_login_pin(Request $request)
  {
    $url = $this->api_url . "change-driver-pin";

    $params          = [
      'driver_id' => $request->id,
      'login_pin' => $request->login_pin,
    ];

    $change_pin_data = FrontModel::callPostCurl($url, $params);
  }


  //function used to update driver
  public function edit_driver(Request $request, $id)
  {

    if ($request->isMethod('post')) {

      $url = $this->api_url . "update-driver-detail";

      $params          = [
        'driver_id' => $id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'photo' => $request->crop_image_id,
        'vehicle_id' => $request->vehicle_id,
      ];

      $driver_update = FrontModel::callPostCurl($url, $params);

      if ($driver_update['success']) {
        return redirect(route('driver-list'))->with('success', $driver_update['message']);
      } else {
        Session::flash('fail', $driver_update['message']);
        return redirect(route('edit-driver', $id));
      }
    } else {
      $url = $this->api_url . "get-driver-detail";

      $params          = array('driver_id' => $id);
      $driver_details = FrontModel::callPostCurl($url, $params);

      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => "Driver Management"],  ['name' => "Edit Category"]
      ];
      return view('/admin/driver-management/edit-driver', [
        'breadcrumbs' => $breadcrumbs,
        'driver_details' => $driver_details['data']
      ]);
    }
  }

  //function used to view driver details
  public function view_driver(Request $request, $id)
  {

    $url = $this->api_url . "get-driver-detail";

    $params          = array('driver_id' => $id);
    $driver_details = FrontModel::callPostCurl($url, $params);

    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => "Driver Management"],  ['name' => "Edit Category"]
    ];
    return view('/admin/driver-management/view-driver', [
      'breadcrumbs' => $breadcrumbs,
      'driver_details' => $driver_details['data']
    ]);
  }

  //function used to change driver status
  public function driver_status(Request $request)
  {
    $url = $this->api_url . "change-driver-status";

    $params          = array('driver_id' => $request->id, 'is_active' => $request->status);
    $driver_status = FrontModel::callPostCurl($url, $params);
  }

  //function used to delete driver 
  public function driver_delete(Request $request)
  {
    $url = $this->api_url . "delete-driver";

    $params          = array('driver_id' => $request->driver_id);
    $driver_delete = FrontModel::callPostCurl($url, $params);

    if ($driver_delete['success']) {
      return redirect(route('driver-list'))->with('success', $driver_delete['message']);
    } else {
      return redirect(route('driver-list'))->with('fail', $driver_delete['message']);
    }
  }

  //function used to show driver all trip list
  public function driver_trip_list($id)
  {
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => "Driver Management"],  ['name' => "Driver Trip List"]
    ];
    return view('/admin/driver-management/driver-trip-list', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  // this function is used for ajax call to show driver list detail
  public function driver_trip_list_detail(Request $request, $id)
  {
    if ($request->ajax()) {
      $data = Trip::where('driver_id', $id)->orderBy('id', 'DESC')->get();
      return Datatables::of($data)
        ->addIndexColumn()

        ->addColumn('name', function ($data) {

          return $data->driver->first_name . ' ' . $data->driver->last_name;
        })

        ->addColumn('trip_generated_at', function ($data) {

          return date("d-M-Y h:i A", strtotime($data->trip_generated_at));
        })

        ->addColumn('action', function ($data) {
          $base_url = url('/');
          $btn = '<a href="' . route('trip-detail', $data->id) . '" class="mr-1"><i class="fas fa-eye"></i></a>';
          return $btn;
        })

        ->rawColumns(['name', 'action', 'trip_generated_at'])
        ->make(true);
    }
  }

}
