<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Driver;
use App\Models\Drivers;
use App\Models\FrontModel;
use App\Models\Trip;
use App\Models\User;
use DataTables;
use Illuminate\Http\Request;
use Session;
use Carbon\Carbon;

class MerchantController extends Controller
{
  private $api_url;
  public function __construct()
  {
    $this->api_url = rtrim(config('app.url'), '/') . '/api/';
  }

  public function generatePassword () 
  {	
    $possible = "0123456789abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ!@#$%^&*";	
    $rand_str = str_shuffle($possible);
    $password = substr($rand_str,strlen($possible)-8);
    return $password;
  }

  //function used to show merchant list
  public function merchant_list()
  {
    $breadcrumbs = [
      ['link' => "/superadmin/analytics", 'name' => "Home"],  ['name' => "Merchant List"]
    ];
    return view('/superadmin/merchant-management/merchant-list', [
      'breadcrumbs' => $breadcrumbs
    ]);
  }

 
  // this function is used for ajax call to show merchant list detail
  public function merchant_list_detail(Request $request)
  {
    if ($request->ajax()) { 
      $data = User::where('user_type', 4)->orderBy('id', 'DESC')->get();
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

        ->addColumn('code', function ($data) {

          return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->merchant_id .'</a>';
        })

        ->addColumn('name', function ($data) {

          return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->merchant_name .'</a>';
        })
        ->addColumn('address', function ($data) {

          return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->merchant_address .'</a>';
        })
        ->addColumn('email', function ($data) {

          return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->email.'</a>';
        })

        ->addColumn('total_trips', function ($data) {

          $trip = Driver::where('user_id', $data->id)->count();

          if ($trip > 0) {
            return '<a href="' . route("driver-list", ['driver'=> base64_encode($data->id)]) . '" style="text-decoration: underline;">' . $trip . '</a>';
          } else {
            return '0';
          }
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
          if ($data->is_active == 1) {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',0)"><i class="fas fa-unlock"></i></a>';
          } else {
            $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',1)"><i class="fas fa-lock"></i></a>';
          }

          $btn = '<a href="' . route('edit-merchant', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
                  <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
          return $btn;
        })

        ->rawColumns(['code','name','address', 'is_active','email', 'total_trips','last_active', 'action'])
        ->make(true);
    }
  }

  // this function is used for ajax call to show trip list detail according to filter
  public function merchant_list_detail_filter(Request $request)
  {

    $name = $request->search_user;
    $email = $request->search_email;
    $phone = $request->search_phone;
    $user_type = $request->user_type;
    $data = ApiModel::fetchAllMerchants($name, $email, $phone, $user_type);

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

    ->addColumn('code', function ($data) {

      return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->merchant_id .'</a>';
    })

    ->addColumn('name', function ($data) {

      return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->merchant_name.'</a>';
    })
    ->addColumn('address', function ($data) {

      return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->merchant_address .'</a>';
    })
    ->addColumn('email', function ($data) {

      return '<a href="' . route('view-merchant', $data->id) . '" class="mr-1">'.$data->email.'</a>';
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
      if ($data->is_active == 1) {
        $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',0)"><i class="fas fa-unlock"></i></a>';
      } else {
        $lock_icon = '<a href="javascript:void(0)" class="mr-1" onclick="enableactive(' . $data->id . ',1)"><i class="fas fa-lock"></i></a>';
      }

      $btn = '<a href="' . route('edit-merchant', $data->id) . '" class="mr-1"><i class="fas fa-pen"></i></a>' . $lock_icon . '
              <a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $data->id . ')" data-target="#deleteSliderConfirm"><i class="fas fa-trash-alt"></a>';
      return $btn;
    })

    ->rawColumns(['code','name','address', 'email', 'is_active', 'total_trips','auth_pin', 'last_active', 'action'])
    ->make(true);
  }

  //function used to add driver details
  public function add_merchant(Request $request)
  {

    if ($request->isMethod('post')) {

      $request->validate([
        "first_name"=>'required',
        "last_name"=>'required',
        "merchant_name"=>'required',
        "email"=>'required|email',
        "phone"=>'required|max:15',
      ]);     
      $url = $this->api_url . "add-merchant";
  
      $params          = [ 
        'merchant_name' => $request->merchant_name,
        'merchant_address' => $request->merchant_address,
        'phone' => $request->phone,
        // 'photo' => $request->crop_image_id,
        'email' => $request->email,
        'first_name'=>$request->first_name,
        'last_name'=>$request->last_name,
        'password'=> $this->generatePassword(),
      ];


      $add_user_data = FrontModel::callPostCurl($url, $params);
      if ($add_user_data['success']) {
        return redirect(route('merchant-list'))->with('success', $add_user_data['message']);
      } else {
        Session::flash('fail', $add_user_data['message']);
        return redirect(route('add-merchant'))->withInput();
      }
    } else {
      
      $breadcrumbs = [
        ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/merchant-list", 'name' => "Merchant Management"],  ['name' => "Add Merchant"]
      ];
      
      return view('/superadmin/merchant-management/add-merchant', [
        'breadcrumbs' => $breadcrumbs
      ]);
    }
  }

  //function used to update driver
  public function edit_merchant(Request $request, $id)
  {

    if ($request->isMethod('post')) {
       $request->validate([
        "first_name"=>'required',
        "last_name"=>'required',
        "merchant_name"=>'required',
        "email"=>'required|email',
        "phone"=>'required|max:15',
      ]);  
      
      $url = $this->api_url . "update-merchant-detail";

      $params          = [
        'user_id' => $id,
        'merchant_name' => $request->merchant_name,
        'merchant_address' => $request->merchant_address,
        'phone' => $request->phone,
        //'photo' => $request->crop_image_id,
        'first_name'=>$request->first_name,
        'last_name'=>$request->last_name,
        'email' => $request->email,
      ];
      $user_update = FrontModel::callPostCurl($url, $params);

      if ($user_update['success']) {
        return redirect(route('merchant-list'))->with('success', $user_update['message']);
      } else {
        Session::flash('fail', $user_update['message']);
        return redirect(route('edit-merchant', $id));
      }
    } else {
      $url = $this->api_url . "get-merchant-detail";

      $params          = array('user_id' => $id);
      $user_details = FrontModel::callPostCurl($url, $params);

      $breadcrumbs = [
        ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/merchant-list", 'name' => "Merchant Management"],  ['name' => "Edit Merchant"]
      ];
      return view('/superadmin/merchant-management/edit-merchant', [
        'breadcrumbs' => $breadcrumbs,
        'user_details' => $user_details['data']
      ]);
    }
  }

  //function used to view user details
  public function view_merchant(Request $request, $id)
  {

    $url = $this->api_url . "get-merchant-detail";

    $params          = array('user_id' => $id);
    $user_details = FrontModel::callPostCurl($url, $params);

    $breadcrumbs = [
      ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/merchant-list", 'name' => "Merchant Management"],  ['name' => "View Merchant"]
    ];
    return view('/superadmin/merchant-management/view-merchant', [
      'breadcrumbs' => $breadcrumbs,
      'user_details' => $user_details['data']
    ]);
  }

  //function used to change driver status
  public function merchant_status(Request $request)
  {
    abort_unless((int) $request->user()->user_type === 1, 403);
    $data = $request->validate(['id' => 'required|integer', 'status' => 'required|in:0,1']);
    $merchant = User::where('user_type', 4)->findOrFail($data['id']);
    $merchant->update(['is_active' => $data['status']]);
    return response()->json(['success' => true, 'message' => 'Merchant status updated successfully.']);
  }

  public function merchant_delete(Request $request)
  {
    abort_unless((int) $request->user()->user_type === 1, 403);
    $data = $request->validate(['user_id' => 'required|integer']);
    User::where('user_type', 4)->findOrFail($data['user_id'])->delete();
    return redirect()->route('merchant-list')->with('success', 'Merchant deleted successfully.');
  }

  public function user_status(Request $request)
  {
    $url = $this->api_url . "change-merchant-status";

    $params          = array('user_id' => $request->id, 'is_active' => $request->status);
    $user_status = FrontModel::callPostCurl($url, $params);
  }

  //function used to delete driver 
  public function user_delete(Request $request)
  {

    $url = $this->api_url . "delete-merchant";

    $params          = array('user_id' => $request->user_id);
    $user_delete = FrontModel::callPostCurl($url, $params);

    if ($user_delete['success']) {
      return redirect(route('merchant-list'))->with('success', $user_delete['message']);
    } else {
      return redirect(route('merchant-list'))->with('fail', $user_delete['message']);
    }
  }

  //function used to show driver all trip list
  public function user_trip_list($id)
  {
    $breadcrumbs = [
      ['link' => "/superadmin/analytics", 'name' => "Home"], ['link' => "/superadmin/driver-list", 'name' => "Driver Management"],  ['name' => "Driver Trip List"]
    ];
    return view('/superadmin/merchant-management/user-trip-list', [
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

  public function merchant_list_data(Request $request){
    $id=$request->id;
    if (!\App\Support\StaffAccess::global($request->user())) abort_unless((int)$id === (int)$request->user()->merchant_assigned,403);
    $data = User::where('user_type', 4)->where('id',$id)->first();
    return $data;
  }

  public function merchant_data(Request $request){
    $query = User::where('user_type', 4)->where('is_active',1);
    if (!\App\Support\StaffAccess::global($request->user())) $query->whereKey($request->user()->merchant_assigned ?: -1);
    $data = $query->get();
    return $data;
  }


}
