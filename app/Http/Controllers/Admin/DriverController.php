<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\FrontModel;
use App\Models\Trip;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class DriverController extends Controller
{
  private $api_url;
  private $allowUserType;
  public function __construct()
  {
    $this->api_url = config('app.url') . 'api/';
    $this->allowUserType = config('custom.permission');
  }

  //function used to show driver list
  public function driver_list(Request $request)
  {
    if (in_array(session('user_role'), $this->allowUserType)) {
      $driver_id = null;
      if ($request->driver) {
        $driver_id = base64_decode($request->driver);
      } else {
        $driver_id = null;
      }
    } else {
      $driver_id = Auth::user()->id;
    }
    $breadcrumbs = [
      ['link' => "/analytics", 'name' => "Home"],  ['name' => "Driver List"]
    ];
    return view('/admin/driver-management/driver-list', [
      'breadcrumbs' => $breadcrumbs, 'user_id' => $driver_id
    ]);
  }

  // this function is used for ajax call to show driver list detail
  public function driver_list_detail(Request $request)
  {
    $user_id = Auth::user()->id;
    $user_type = Auth::user()->user_type;
    if ($request->ajax()) {
      $merchant_id = $request->merchant_id;
      $vehicle = $request->search_vehicle;
      $company_user = $request->company_user;
      $is_active = $request->status;

      $where = [];
      if (!empty($merchant_id)) {
        $where[] = ['merchant_id', $merchant_id];
      }
      if (!empty($vehicle)) {
        $where[] = ['vehicle_id', 'like', '%' . $vehicle . '%'];
      }
      if (!empty($company_user)) {
        $where[] = ['user_id', $company_user];
      }
      if ($is_active != "" || !is_null($is_active)) {
        $where[] = ['is_active', $is_active];
      }
      if ($user_type == 3) { // user
        $data = Driver::where('user_id', $user_id)->where($where)->orderBy('id', 'DESC')->get();
      } else { //admin and superadmin
        $data = Driver::where($where)->orderBy('id', 'DESC')->get();
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
          return '<a href="' . route('view-driver', $data->id) . '" class="mr-1">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })
        ->addColumn('merchant_name', function ($data) {
            $user = User::select('merchant_name')->where('id', $data->merchant_id)->first();
            if ($user) {
              if (session('user_role') == 1) {
                return '<a href="' . route('view-user', $data->user_id) . '" class="mr-1">' . $user->merchant_name . '</a>';
              }
              return $user->merchant_name;
            }
        })
        ->addColumn('user_name', function ($data) {
          $user = User::select('first_name', 'last_name')->where('id', $data->user_id)->first();
          if ($user) {
            if (session('user_role') == 1) {
              return '<a href="' . route('view-user', $data->user_id) . '" class="mr-1">' . $user->first_name . ' ' . $user->last_name . '</a>';
            } else {
              return $user->first_name . ' ' . $user->last_name;
            }
          }
        })
        ->addColumn('vehicle_id', function ($data) {
          return $data->vehicle_id;
        })
        ->addColumn('total_trips', function ($data) {
          $trip = Trip::where('driver_id', $data->id)->count();

          if ($trip > 0) {
            return $trip;
          } else {
            return 'No Trip';
          }
        })
        ->addColumn('auth_pin', function ($data) {
          $login = '<input type="password" maxlength="4" onkeypress="return onlyNumberKey(event)"  class="form-control" id="login_pin_input' . $data->id . '" value="' . $data->auth_pin . '" style="width: 40%;" autocomplete="off" onblur="save_pin(' . $data->id . ',' . $data->auth_pin . ')" />';
          return $login;
        })
        ->addColumn('last_active', function ($data) {
          if ($data->last_active) {
            return Carbon::parse($data->last_active)->diffForHumans();
          } else {
            return 'Not Logged in yet';
          }
        })
        ->addColumn('action', function ($data) {
          if ($data->is_active == 1) {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',0)"><i class="fas fa-unlock"></i></a>';
          } else {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',1)"><i class="fas fa-lock"></i></a>';
          }

          $btn = '<a href="' . route('edit-driver', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
                  <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
          return $btn;
        })
        ->rawColumns(['name', 'is_active', 'user_name', 'merchant_name', 'total_trips', 'auth_pin', 'last_active', 'action'])
        ->make(true);
    }
  }

  //function used to add driver details
  public function add_driver(Request $request)
  {

    if ($request->isMethod('post')) {
      $request->validate([
        'login_pin'            => 'required',
        "first_name"           => 'required',
        "phone"                => ['required', Rule::unique('drivers')->whereNull('deleted_at'), 'max:12'],
        'vehicle_id'           => ['required', Rule::unique('drivers')->whereNull('deleted_at')],
        'voice_number'         => ['required', Rule::unique('drivers')->whereNull('deleted_at'), 'max:16'],
        'device_serial_number' => ['required', Rule::unique('drivers')->whereNull('deleted_at'), 'max:16'],
      ]);

      if (Auth::user()->user_type != 1 && Auth::user()->user_type != 4) {
        $user_id = Auth::user()->id;
        $merchant = Auth::user()->merchant_assigned;
      } else {
        $request->validate([
          "merchant" => 'required',
          "user_id" => 'required',

        ]);
        $user_id = $request->user_id;
        $merchant = $request->merchant;
      }
      if (!empty($request->billing_term)) {
        $billing_term = $request->billing_term;
      } else {
        $billing_term = 12;
      }
      $params          = [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'photo' => $request->crop_image_id,
        'vehicle_id' => $request->vehicle_id,
        'login_pin' => $request->login_pin,
        'user_id' => $user_id,
        'device_serial_number' => $request->device_serial_number,
        'voice_number' => $request->voice_number,
        'billing_term' => $billing_term,
        'merchant_id' => $merchant,
      ];
      $add_vehicle_params          = [
        'action' => "add_vehicle",
        'registration_no' => $request->vehicle_id,
        'device_serial' => $request->device_serial_number,
        'sim_no' => $request->phone,
        'voice_no' => $request->voice_number,
        'billing_term' => $request->billing_term,
        'user_name' => "bd@epixelsoftware.com",
        'hash_key' => "DABHHJIFELMIWAVS"
      ];
      // Check driver already or not in database===============
      $driver_url = $this->api_url . "get-driver-detail";
      $vehicle = [
        'vehicle_id' => $request->vehicle_id,
        'device_serial_number' => $request->device_serial_number,
      ];
      $driver_data = FrontModel::callPostCurl($driver_url, $vehicle);
      if (isset($driver_data['data'])) {
        $params['driver_id'] = $driver_data['data']['id'];
        $url = $this->api_url . "update-driver-detail";
        $add_driver_data = FrontModel::callPostCurl($url, $params);
        if ($add_driver_data['success']) {
          $auth_pin = $driver_data['data']['login_pin'];
        }
      } else {
        $url = $this->api_url . "add-driver";
        $add_driver_data = FrontModel::callPostCurl($url, $params);
        if ($add_driver_data['success']) {
          $auth_pin = $add_driver_data['login_pin'];
        }
      }
      //===============================\\

      //// Check driver already or not in zypsa.com===============
      $search_vehicle_url = "https://app.zypsa.com/vehicle_result.php?action=search_json&user_name=bd@epixelsoftware.com&hash_key=DABHHJIFELMIWAVS";
      $search_vehicle_params          = [
        'user_name' => "bd@epixelsoftware.com",
        'hash_key' => "DABHHJIFELMIWAVS"
      ];
      $search_vehicle_data = FrontModel::callPostCurl($search_vehicle_url, json_encode($search_vehicle_params));
      foreach ($search_vehicle_data['data']['return_json'] as $value) {
        if ($value['registration_no'] == $request->vehicle_id && $value['device_serial'] == $request->device_serial_number) {
          $add_vehicle_params['data_format'] = "JSON";
          $add_vehicle_params['serial_no'] = $request->device_serial_number;
          $add_vehicle_params['device_id'] = $value['device_id'];
          $driver_key = array_search($driver_data['data']['vehicle_id'], array_column($search_vehicle_data['data']['return_json'], 'registration_no'));
          $add_vehicle_url = "https://app.zypsa.com/vehicle_result.php?action=vehicleedit&user_name=bd@epixelsoftware.com&hash_key=DABHHJIFELMIWAVS&data_format=JSON&device_id=" . $search_vehicle_data['data']['return_json'][$driver_key]['device_id'] . "&registration_no=" . $request->vehicle_id . "&device_serial=" . $request->device_serial_number . "&serial_no=" . $request->device_serial_number . "&voice_no=" . $request->voice_number . "&sim_no=" . $request->phone . "&billing_term=" . $request->billing_term;
        } else {
          $add_vehicle_url = "https://app.zypsa.com/vehicle_api.php";
        }
      }
      //====================================\\

      if ($add_driver_data['success']) {
        FrontModel::callPostCurl($add_vehicle_url, json_encode($add_vehicle_params));
        return redirect(route('driver-list'))->with('success', $add_driver_data['message'] . ' Driver Login Pin is ' . $auth_pin);
      } else {
        Session::flash('fail', $add_driver_data['message']);
        return redirect(route('add-driver'))->withInput();
      }
    } else {
      $url = $this->api_url . "get-merchant-detail";
      $params       = array('user_type' => 4);
      $merchant_detail = FrontModel::callPostCurl($url, $params);
      if ($merchant_detail['success']) {
        $merchant_details = $merchant_detail['data'];
      } else {
        $merchant_details = [];
      }
      if (Auth::user()->user_type == 1) {
        $title = 'Driver Management';
      } else {
        $title = 'Drivers';
      }

      if (Auth::user()->user_type == 1)
        $breadcrumbs = [
          ['link' => "/analytics", 'name' => "Home"], ['link' => "/driver-list", 'name' => "Driver Management"],  ['name' => "Add Driver"]
        ];
      else
        $breadcrumbs = [
          ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => $title],  ['name' => "Add Driver"]
        ];

      return view('/admin/driver-management/add-driver', [
        'breadcrumbs' => $breadcrumbs,
        'merchant_details' => $merchant_details
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

    FrontModel::callPostCurl($url, $params);
  }

  //function used to update driver
  public function edit_driver(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $request->validate(
        [
          "user_id"   =>  'required|integer',
          "first_name" => 'required',
          "merchant"  => 'required|integer',
          "phone" => [
            'required',
            Rule::unique('drivers')->withoutTrashed()->ignore($id),
          ],
          'vehicle_id' => [
            'required',
          ],
          'device_serial_number' => [
            'required', 'max:16',
          ],
          'voice_number' => [
            'required', 'max:16',
          ],

        ],
        [
          'user_id.required'  => 'This field is requied',
          'user_id.integer'  => 'Please select valid user!',

          'merchant.required'  => 'This field is requied',
          'merchant.integer'  => 'Please select valid merchant!',
        ]
      );
      $url = $this->api_url . "update-driver-detail";

      $params          = [
        'driver_id' => $id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'photo' => $request->crop_image_id,
        'vehicle_id' => $request->vehicle_id,
        'device_serial_number' => $request->device_serial_number,
        'voice_number' => $request->voice_number,
        'merchant_id'  => $request->merchant,
        'billing_term' => $request->billing_term
      ];

      $params['user_id'] = $request->user_id; //if superadmin edit driver

      if ($params['user_id'] < 1) { // if user_id is small than 1 return error msg
        return back()->with('fail', 'Something went wrong!!');
      }
      //  =============================================================================
      //                    this api fetch all the data
      // ==============================================================================

      $search_vehicle_url = "https://app.zypsa.com/vehicle_result.php?action=search_json&user_name=bd@epixelsoftware.com&hash_key=DABHHJIFELMIWAVS";
      $search_vehicle_params          = [
        'user_name' => "bd@epixelsoftware.com",
        'hash_key' => "DABHHJIFELMIWAVS"
      ];
      $search_vehicle_data = FrontModel::callPostCurl($search_vehicle_url, json_encode($search_vehicle_params));

      //  =============================================================================
      //                    this api fetch driver data from db
      // ==============================================================================
      $get_url = $this->api_url . "get-driver-detail";
      $get_params          = array('driver_id' => $id);
      $driver_details = FrontModel::callPostCurl($get_url, $get_params);

      $driver_key = array_search($driver_details['data']['vehicle_id'], array_column($search_vehicle_data['data']['return_json'], 'registration_no'));

      //  =============================================================================
      //                    this api update driver data
      // ==============================================================================

      $edit_vehicle_url = "https://app.zypsa.com/vehicle_result.php?action=vehicleedit&user_name=bd@epixelsoftware.com&hash_key=DABHHJIFELMIWAVS&data_format=JSON&device_id=" . $search_vehicle_data['data']['return_json'][$driver_key]['device_id'] . "&registration_no=" . $request->vehicle_id . "&device_serial=" . $request->device_serial_number . "&serial_no=" . $request->device_serial_number . "&voice_no=" . $request->voice_number . "&sim_no=" . $request->phone . "&billing_term=" . $request->billing_term;
      $edit_vehicle_params          = [
        'user_name' => "bd@epixelsoftware.com",
        'hash_key' => "DABHHJIFELMIWAVS",
        'data_format' => "JSON",
        'device_id' => $search_vehicle_data['data']['return_json'][$driver_key]['device_id'],
        'registration_no' => $request->vehicle_id,
        'serial_no' => $request->device_serial_number,
        'voice_no' => $request->voice_number,
        'billing_term' => $request->billing_term
      ];

      $driver_update = FrontModel::callPostCurl($url, $params);

      FrontModel::callPostCurl($edit_vehicle_url, json_encode($edit_vehicle_params));

      if ($driver_update['success']) {
        return redirect(route('driver-list'))->with('success', $driver_update['message']);
      } else {
        Session::flash('fail', $driver_update['message']);
        return redirect(route('edit-driver', $id));
      }
    } else {
      // get request
      $url = $this->api_url . "get-driver-detail";
      $params          = array('driver_id' => $id);
      $driver_details = FrontModel::callPostCurl($url, $params);
      $Murl = $this->api_url . "get-merchant-detail";
      $Mparams       = array('user_type' => 4);
      $merchant_detail = FrontModel::callPostCurl($Murl, $Mparams);
      if ($merchant_detail['success']) {
        $merchant_details = $merchant_detail['data'];
      } else {
        $merchant_details = [];
      }

      if (Auth::user()->user_type == 1)
        $breadcrumbs = [
          ['link' => "/analytics", 'name' => "Home"], ['link' => "/driver-list", 'name' => "Driver Management"],  ['name' => "Edit Driver"]
        ];
      else
        $breadcrumbs = [
          ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => "Driver Management"],  ['name' => "Edit Driver"]
        ];
      return view('/admin/driver-management/edit-driver', [
        'breadcrumbs' => $breadcrumbs,
        'driver_details' => $driver_details['data'],
        'merchant_details' => $merchant_details
      ]);
    }
  }

  //function used to view driver details
  public function view_driver(Request $request, $id)
  {
    $url = $this->api_url . "get-driver-detail";
    $params          = array('driver_id' => $id);
    $driver_details = FrontModel::callPostCurl($url, $params);

    if (Auth::user()->user_type == 1)
      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "driver-list", 'name' => "Driver Management"],  ['name' => "View Driver"]
      ];
    else
      $breadcrumbs = [
        ['link' => "/analytics", 'name' => "Home"], ['link' => "/admin/driver-list", 'name' => "Driver Management"],  ['name' => "View Driver"]
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
    FrontModel::callPostCurl($url, $params);

    $get_url = $this->api_url . "get-driver-detail";
    $get_params     = array('driver_id' => $request->id);
    $driver_details = FrontModel::callPostCurl($get_url, $get_params);

    $search_vehicle_url = "https://app.zypsa.com/vehicle_result.php?action=search_json&user_name=bd@epixelsoftware.com&hash_key=DABHHJIFELMIWAVS";
    $search_vehicle_params          = [
      'user_name' => "bd@epixelsoftware.com",
      'hash_key' => "DABHHJIFELMIWAVS"
    ];
    $search_vehicle_data = FrontModel::callPostCurl($search_vehicle_url, json_encode($search_vehicle_params));

    $driver_key = array_search($driver_details['data']['vehicle_id'], array_column($search_vehicle_data['data']['return_json'], 'registration_no'));

    $request_status = ($request->status == 0) ? 1 : 0;
    $change_vehicle_status_url = "https://app.zypsa.com/vehicle_result.php?action=vehicleedit&user_name=bd@epixelsoftware.com&hash_key=DABHHJIFELMIWAVS&data_format=JSON&device_id=" . $search_vehicle_data['data']['return_json'][$driver_key]['device_id'] . "&device_active=" . $request_status;
    $change_vehicle_status_params          = [
      'user_name' => "bd@epixelsoftware.com",
      'hash_key' => "DABHHJIFELMIWAVS",
      'data_format' => "JSON",
      'device_id' => $search_vehicle_data['data']['return_json'][$driver_key]['device_id'],
      'device_active' => $request_status
    ];
    FrontModel::callPostCurl($change_vehicle_status_url, json_encode($change_vehicle_status_params));
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
          $btn = '<a href="' . route('trip-detail', $data->id) . '" class="mr-1"><i class="fas fa-eye"></i></a>';
          return $btn;
        })
        ->rawColumns(['name', 'action', 'trip_generated_at'])
        ->make(true);
    }
  }
}
