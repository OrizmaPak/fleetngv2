<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Driver;
use App\Models\FrontModel;
use App\Models\Trip;
use App\Models\User;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
  private $api_url;
  public function __construct()
  {
    $this->api_url = rtrim(config('app.url'), '/') . '/api/';
  }


  public function generatePassword()
  {
    $possible = "0123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ!@#$%^&*";
    $rand_str = str_shuffle($possible);
    $password = substr($rand_str, strlen($possible) - 8);
    return $password;
  }

  //function used to show driver list
  public function user_list()
  {
    $breadcrumbs = [
      ['link' => "/superadmin/analytics", 'name' => "Home"],  ['name' => "User List"]
    ];
    return view('/superadmin/user-management/user-list', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }


  // this function is used for ajax call to show driver list detail
  public function user_list_detail(Request $request)
  {
    if ($request->ajax()) {
      $data = User::where('user_type', 3)->orderBy('id', 'DESC')->get();
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
          return '<a href="' . route('view-user', $data->id) . '" class="mr-1">' . $data->first_name . ' ' . $data->last_name . '</a>';
        })
        ->addColumn('email', function ($data) {
          return '<a href="' . route('view-user', $data->id) . '" class="mr-1">' . $data->email . '</a>';
        })

        ->addColumn('role', function ($data) {
          return $data->is_payment_user ? 'Payment User' : 'Normal User';
        })

        ->addColumn('total_trips', function ($data) {

          $trip = Driver::where('user_id', $data->id)->count();

          if ($trip > 0) {
            return '<a href="' . route("driver-list", ['driver' => base64_encode($data->id)]) . '" style="text-decoration: underline;">' . $trip . '</a>';
          } else {
            return '0';
          }
        })

        ->addColumn('last_active', function ($data) {
          if ($data->last_active) {
            return Carbon::parse($data->last_active)->diffForHumans();
          } else {
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

          $btn = '<a href="' . route('edit-user', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
                  <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
          return $btn;
        })

        ->rawColumns(['name', 'is_active', 'email', 'role', 'total_trips', 'last_active', 'action'])
        ->make(true);
    }
  }

  // this function is used for ajax call to show trip list detail according to filter
  public function user_list_detail_filter(Request $request)
  {

    $name = $request->search_user;
    $email = $request->search_email;
    $phone = $request->search_phone;
    $user_type = $request->user_type;


    // $daterange;

    $data = ApiModel::fetchAllUsers($name, $email, $phone, $user_type);

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

        return '<a href="' . route('view-user', $data->id) . '" class="mr-1">' . $data->first_name . ' ' . $data->last_name . '</a>';
      })
      ->addColumn('email', function ($data) {
        return '<a href="' . route('view-user', $data->id) . '" class="mr-1">' . $data->email . '</a>';
      })

      ->addColumn('role', function ($data) {
        return $data->is_payment_user ? 'Payment User' : 'Normal User';
      })
      
      ->addColumn('total_trips', function ($data) {

        $trip = Driver::where('user_id', $data->id)->count();
        if ($trip > 0) {
          return '<a href="' . route("driver-list", $data->id) . '" style="text-decoration: underline;">' . $trip . '</a>';
        } else {
          return '0';
        }
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

        $btn = '<a href="' . route('edit-user', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
              <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
        return $btn;
      })

      ->rawColumns(['name', 'email', 'role', 'is_active', 'total_trips', 'auth_pin', 'last_active', 'action'])
      ->make(true);
  }

  //function used to add driver details
  public function add_user(Request $request)
  {
    if ($request->isMethod('post')) {

      $request->validate([
        "first_name" => 'required',
        "email" => 'required|email',
        "phone" => 'required',
      ]);



      $url = $this->api_url . "add-user";

      $params          = [
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'merchant_assigned' => $request->merchant,
        'is_payment_user' => $request->payment_user ? 1 : 0,
        'password' => $this->generatePassword()
      ];

      $add_user_data = FrontModel::callPostCurl($url, $params);
      if ($add_user_data['success']) {
        return redirect(route('user-list'))->with('success', $add_user_data['message']);
      } else {
        Session::flash('fail', $add_user_data['message']);
        return back()->withInput();
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

      $breadcrumbs = [
        ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/user-list", 'name' => "User Management"],  ['name' => "Add User"]
      ];

      return view('/superadmin/user-management/add-user', [
        'breadcrumbs' => $breadcrumbs,
        'merchant_details' => $merchant_details
      ]);
    }
  }

  //function used to update driver
  public function edit_user(Request $request, $id)
  {
    if ($request->isMethod('post')) {
      $url = $this->api_url . "update-user-detail";

      $params          = [
        'user_id' => $id,
        'first_name' => $request->first_name,
        'last_name' => $request->last_name,
        'phone' => $request->phone,
        'email' => $request->email,
        'merchant_assigned' => $request->merchant,
      ];

      $user_update = FrontModel::callPostCurl($url, $params);

      if ($user_update['success']) {
        return redirect(route('user-list'))->with('success', $user_update['message']);
      } else {
        Session::flash('fail', $user_update['message']);
        return redirect(route('edit-user', $id));
      }
    } else {

      $url1 = $this->api_url . "get-user-detail";

      $params1       = array('user_id' => $id);
      $user_details = FrontModel::callPostCurl($url1, $params1);

      $url2 = $this->api_url . "get-merchant-detail";

      $params2      = array('user_type' => 4);
      $merchant_detail = FrontModel::callPostCurl($url2, $params2);
      if ($merchant_detail['success']) {
        $merchant_details = $merchant_detail['data'];
      } else {
        $merchant_details = [];
      }
      $breadcrumbs = [
        ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/user-list", 'name' => "User Management"],  ['name' => "Edit User"]
      ];
      return view('/superadmin/user-management/edit-user', [
        'breadcrumbs' => $breadcrumbs,
        'user_details' => $user_details['data'],
        'merchant_details' => $merchant_details
      ]);
    }
  }

  //function used to view user details
  public function view_user(Request $request, $id)
  {
    $url = $this->api_url . "get-user-detail";

    $params          = array('user_id' => $id);
    $user_details = FrontModel::callPostCurl($url, $params);

    $breadcrumbs = [
      ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/user-list", 'name' => "User Management"],  ['name' => "Edit Category"]
    ];
    return view('/superadmin/user-management/view-user', [
      'breadcrumbs' => $breadcrumbs,
      'user_details' => $user_details['data']
    ]);
  }

  //function used to change driver status
  public function user_status(Request $request)
  {
    $url = $this->api_url . "change-user-status";

    $params          = array('user_id' => $request->id, 'is_active' => $request->status);
    FrontModel::callPostCurl($url, $params);
  }

  //function used to delete driver 
  public function user_delete(Request $request)
  {
    $url = $this->api_url . "delete-user";
    $params          = array('user_id' => $request->user_id);
    $user_delete = FrontModel::callPostCurl($url, $params);

    if ($user_delete['success']) {
      return back()->with('success', $user_delete['message']);
    } else {
      return back()->with('fail', $user_delete['message']);
    }
  }

  //function used to show driver all trip list
  public function user_trip_list($id)
  {
    $breadcrumbs = [
      ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/driver-list", 'name' => "Driver Management"],  ['name' => "Driver Trip List"]
    ];
    return view('/superadmin/user-management/user-trip-list', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

  // this function is used for ajax call to show driver list detail
  public function user_trip_list_detail(Request $request, $id)
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

  public function user_data(Request $request)
  {
    $id = $request->id;
    if (!empty($id)) {
      $where = ['is_active' => 1, 'merchant_assigned' => $id, 'user_type' => 3];
      $query = User::where($where);
      if (!\App\Support\StaffAccess::global($request->user())) $query->whereKey($request->user()->id);
      $userData = $query->get();
    } else {
      $userData = '';
    }
    return $userData;
  }
}
