<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ApiModel;
use App\Models\Driver;
use App\Models\PickupLocation;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\Helper;
use App\Models\TripRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ApiController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth:sanctum')->except(['forgot_password','admin_login','superadmin_login']);
    $this->middleware(function ($request, $next) {
      \App\Support\StaffAccess::api($request->route()->getActionMethod(), $request);
      return $next($request);
    });
  }
  //function used for superadmin login
  public function superadmin_login(Request $request)
  {

    $email        = $request->email;
    $password     = $request->password;
    $user_type = $request->user_type;
    $where_data   = array('email' => $email, 'user_type' => 1);

    $user = User::where($where_data)->first();
    if ($user &&  Hash::check($password, $user->password)) {
      $where_data   = array('email' => $email, 'user_type' => $user_type);
      $user_detail  = ApiModel::fetchSingleRecord('users', $where_data);

      if ($user_detail->is_active == 0) {
        $response['success'] = false;
        $response['message'] = "Error! Your account have blocked by admin. Please contact with admin for continue your account.";
      } else {

        $temp = [];

        $temp['id'] = $user_detail->id;

        $response['success']      = true;
        $response['message']      = "Success! Login Successfully";
        $response['user_details'] = $temp;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Invalid Email or password.";
    }
    return json_encode($response);
  }

  //function used for admin login
  public function admin_login(Request $request)
  {

    $email        = $request->email;
    $password     = $request->password;
    $user_type = $request->user_type;
    if (Auth::attempt(array('email' => $email, 'password' => $password, 'user_type' => $user_type))) {

      $where_data   = array('email' => $email, 'user_type' => $user_type);
      $user_detail  = ApiModel::fetchSingleRecord('users', $where_data);

      if ($user_detail->is_active == 0) {
        $response['success'] = false;
        $response['message'] = "Error! Your account have blocked by admin. Please contact with admin for continue your account.";
      } else {
        User::where('id', Auth::id())->update(['last_active' => Carbon::now()]); // Update last login activity
        $temp = [];
        $temp['id']             = $user_detail->id;

        $response['success']      = true;
        $response['message']      = "Success! Login Successfully";
        $response['user_details'] = $temp;
      }
      return json_encode($response);
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Invalid Email or password.";
    }
    return json_encode($response);
  }

  public function forgot_password(Request $request)
  {

    $email = $request->input('email');

    $where_data   = array('email' => $email);
    $user_detail  = ApiModel::fetchSingleRecord('users', $where_data);

    if (empty($user_detail)) {
      $response['success'] = False;
      $response['message'] = "Error! Email is invalid! Please try valid email.";
    } else {
      $code = \Illuminate\Support\Str::random(64);
      \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(['email'=>$email],['token'=>hash('sha256',$code),'created_at'=>now()]);
      $forgot_data  = array('token' => $code);
      ApiModel::editRecord('users', $where_data, $forgot_data);

      //Send mail to registered user for email verification.              
      $user_id = bcrypt($user_detail->id);
      $url = url('/') . '/reset-password/' . $code . '?signature=' . $user_id . '&type=' . $user_detail->user_type;
      $name = $user_detail->first_name . ' ' . $user_detail->last_name;
      $today_date = date('M d, Y');
      $data = array('email' => $email, 'name' => $name, 'url' => $url, 'today_date' => $today_date, 'subject' => 'Change Password');

      Mail::send('mails.reset-password', $data, function ($message) use ($data) //Send Mail
      {
        $message->to($data['email'])->subject('Change Password');
      });

      $response['success'] = true;
      $response['message'] = "Success! A password reset link has been sent. Please check your main inbox or spam folder.";
    }

    return json_encode($response);
  }

  //function used to change password
  public function change_password(Request $request)
  {
    $user_id      = $request->user_id;
    $new_password = $request->new_password;
    $old_password = $request->old_password;

    $check_data   = array('id' => $user_id);
    $user_detail   = ApiModel::checkSingleRecord('users', $check_data);

    if (!empty($user_detail)) {
      if (!empty($new_password)) {
        if (!Hash::check($old_password, $user_detail->password)) {
          $response['success'] = false;
          $response['message'] = "Error! Wrong old password!.";
        } else {
          $where_data     = array('id' => $user_id);
          $password_data  = array('password' => bcrypt($new_password));
          $update         = ApiModel::editRecord('users', $where_data, $password_data);

          if ($update) {
            $response['success'] = true;
            $response['message'] = "Success! Password has changed successfully.";
          } else {
            $response['success'] = false;
            $response['message'] = "Error! Please try again!";
          }
        }
      } else {
        $response['success'] = false;
        $response['message'] = "Error! New Password is empty!";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Invalid user id!";
    }

    return json_encode($response);
  }

  //function used to add driver details
  public function add_driver(Request $request)
  {
    if ($request->first_name && $request->phone && $request->photo && $request->vehicle_id && $request->login_pin && $request->user_id) {
      $check = Driver::where('phone', $request->phone)->count();
      if ($check == 0) {
        if ($request->photo) {
          $crop_image = $request->photo;

          list($type, $crop_image) = explode(';', $crop_image);
          list(, $crop_image)      = explode(',', $crop_image);
          $image = base64_decode($crop_image);

          $profile_img = 'driver/img-' . time() . '.png';
          Helper::addS3Image($profile_img, $image);
        } else {
          $profile_img = NULL;
        }

        $auth_pin = $request->login_pin;

        $last_name = $request->last_name;
        if ($request->last_name == 'null') {
          $last_name = '';
        } else if (empty($request->last_name)) {
          $last_name = '';
        }
        $driver = Driver::create([
          'first_name' => $request->first_name,
          'last_name' => $last_name,
          'phone' => $request->phone,
          'photo' => $profile_img,
          'vehicle_id' => $request->vehicle_id,
          'auth_pin' => $auth_pin,
          'user_id' => $request->user_id,
          'device_serial_number' => $request->device_serial_number,
          'voice_number' => $request->voice_number,
          'billing_term' => $request->billing_term,
          'merchant_id' => $request->merchant_id,
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Driver added successfully.";
        $response['login_pin'] = $driver->auth_pin;
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Phone number already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to update driver details
  public function update_driver_detail(Request $request)
  {
    if ($request->driver_id) {

      $driver = Driver::where('id', $request->driver_id)->first();

      $check = Driver::where('id', '!=', $request->driver_id)->where('phone', '==', $request->phone)->first();
      if ($check == 0) {

        if ($request->photo) {
          $crop_image = $request->photo;
          list($type, $crop_image) = explode(';', $crop_image);
          list(, $crop_image)      = explode(',', $crop_image);
          $image = base64_decode($crop_image);

          $profile_img = 'driver/img-' . time() . '.png';
          Helper::addS3Image($profile_img, $image);

          Helper::deleteS3Image($driver->photo); //delete old s3 image if exists 
        } else {
          $profile_img = $driver->photo;
        }

        $last_name = $request->last_name;
        if ($request->last_name == 'null') {
          $last_name = '';
        } else if (empty($request->last_name)) {
          $last_name = '';
        }

        $data = [
          'first_name' => $request->first_name,
          'last_name' => $last_name,
          'phone' => $request->phone,
          'photo' => $profile_img,
          'vehicle_id' => $request->vehicle_id,
          'device_serial_number' => $request->device_serial_number,
          'voice_number' => $request->voice_number,
          'merchant_id'  => $request->merchant_id,
          'billing_term' => $request->billing_term
        ];

        if ($request->user_id) {
          $data['user_id'] = $request->user_id;
        }

        $driver->update($data);

        $response['success'] = true;
        $response['message'] = "Success! Driver Updated successfully.";
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Phone number already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }
  //function used to change driver login pin
  public function change_driver_pin(Request $request)
  {
    if ($request->driver_id && $request->login_pin) {
      $driver = Driver::where('id', $request->driver_id)->first();

      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {

        $driver->update(['auth_pin' => $request->login_pin]);

        $response['success'] = true;
        $response['message'] = "Success! Driver Login pin changed successfully";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to get driver details
  public function get_driver_detail(Request $request)
  {
    if ($request->driver_id || $request->vehicle_id) {
      if (isset($request->driver_id)) {
        $driver = Driver::where('id', $request->driver_id)->first();
      }
      if (isset($request->vehicle_id)) {
        $driver = Driver::where('vehicle_id', $request->vehicle_id)->where('device_serial_number', $request->device_serial_number)->first();
      }

      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {
        $response['success'] = true;
        $response['message'] = "Success! Driver found";

        $temp['id']          =   $driver->id;
        $temp['first_name']  =   $driver->first_name;
        $temp['last_name']   =   $driver->last_name;
        $temp['phone']       =   $driver->phone;
        $temp['email']       =   $driver->email;
        $temp['photo']       =   Helper::imageUrl($driver->photo);
        $temp['vehicle_id']  =   $driver->vehicle_id;
        $temp['login_pin']   =   $driver->auth_pin;
        $temp['device_serial_number']   =   $driver->device_serial_number;
        $temp['voice_number']   =   $driver->voice_number;
        $temp['billing_term']   =   $driver->billing_term;
        $temp['merchant_id']   =   $driver->merchant_id;
        $temp['user_id']   =   $driver->user_id;

        $response['data'] = $temp;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to change driver status
  public function change_driver_status(Request $request)
  {
    if ($request->driver_id && ($request->is_active) != '') {

      $driver = Driver::where('id', $request->driver_id)->first();

      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {
        $driver->update([
          'is_active' => $request->is_active
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Driver status updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to delete driver details
  public function delete_driver(Request $request)
  {
    if ($request->driver_id) {
      $driver = Driver::where('id', $request->driver_id)->first();
      if (!$driver) {
        $response['success'] = false;
        $response['message'] = "Error! Driver not found.";
      } else {
        $driver->delete();

        $response['success'] = true;
        $response['message'] = "Success! Driver deleted successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to get trip details
  public function get_trip_detail(Request $request)
  {
    if ($request->trip_id) {
      $trip = Trip::with('driver', 'dropLocation', 'pickupLocation')->where('id', $request->trip_id)->first();
      $payment = $trip->payment();
      $payment_status =  '--';

      if ($payment && $payment->transaction_id) {
        if (($trip->status == 1 || $trip->status == 2) && !$payment->transaction_id) {
          $payment_status =  '--';
        } else if ($trip->status == 3 && !$payment->transaction_id) {
          $payment_status = 'Pending';
        } else if ($payment->transaction_id) {
          $payment_status = 'Paid';
        } else if ($payment->transaction_id && $payment->status !== 'successful') {
          $payment_status = 'Failed';
        } else {
          $payment_status = '--';
        }
      }

      $trip->payment = $payment;
      $trip->payment_status = $payment_status;
      $trip->total_trip_cost = $trip->total_trip_cost();

      if (!$trip) {
        $response['success'] = false;
        $response['message'] = "Error! Trip not found.";
      } else {
        $response['success'] = true;
        $response['message'] = "Success! Trip found";
        $response['data'] = $trip;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }


  //function used to cancel trip details
  public function cancel_trip(Request $request)
  {
    if ($request->trip_id) {

      $trip = Trip::where('id', $request->trip_id)->first();

      if (!$trip) {
        $response['success'] = false;
        $response['message'] = "Error! Trip not found.";
      } else {

        $trip->update(['deleted_at' => now(), 'status' => 4]);

        $response['success'] = true;
        $response['message'] = "Success! Trip canceled successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to delete trip details
  public function delete_trip(Request $request)
  {
    if ($request->trip_id) {

      $trip = Trip::where('id', $request->trip_id)->first();
      
      if (!$trip) {
        $response['success'] = false;
        $response['message'] = "Error! Trip not found.";
      } else {
        TripRequest::where(['trip_id' => $trip->id, 'driver_id' => $trip->driver_id])->delete(); //  remove all previous trip requests
        $trip->delete();
        $response['success'] = true;
        $response['message'] = "Success! Trip deleted successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }


  //function used to delete trip details
  public function change_trip_driver(Request $request)
  {
    if ($request->trip_id && $request->driver_id) {
      $trip = Trip::where('id', $request->trip_id)->first();
      if (!$trip) {
        $response['success'] = false;
        $response['message'] = "Error! Trip not found.";
      } else {
        if ($trip->driver_id !== intval($request->driver_id)) {
          TripRequest::where(['trip_id' => $trip->id, 'driver_id' => $trip->driver_id])->delete(); //  remove all previous trip requests
          TripRequest::create(['trip_id' => $trip->id, 'driver_id' => $request->driver_id, 'client_id' => $trip->client_id]); // add new trip request for new driver 
          $trip->driver_id = $request->driver_id;
          $trip->status = 1; // set trip status as new trip
          $trip->save();
        }
        $response['success'] = true;
        $response['message'] = "Success! Trip driver changed successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }
    return json_encode($response);
  }

  //function used to add pickup location details
  public function add_pickup_location(Request $request)
  {
    if ($request->user_id && $request->location && $request->latitude && $request->longitude) {
      $check = PickupLocation::where('location_name', $request->location_name)->where('user_id', $request->user_id)->count();
      if ($check == 0) {

        PickupLocation::create([
          'location' => $request->location,
          'location_name' => $request->location_name,
          'latitude' => $request->latitude,
          'longitude' => $request->longitude,
          'user_id'  => $request->user_id
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Pickup location added successfully.";
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Pickup location name already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to get pickup location details
  public function get_pickup_location_detail(Request $request)
  {
    if ($request->location_id) {
      $location = PickupLocation::where('id', $request->location_id)->first();
      if (!$location) {
        $response['success'] = false;
        $response['message'] = "Error! Location not found.";
      } else {
        $response['success'] = true;
        $response['message'] = "Success! Pickup Location found";

        $temp['id']          =   $location->id;
        $temp['location']    =   $location->location;
        $temp['location_name']    =   $location->location_name;
        $temp['latitude']    =   $location->latitude;
        $temp['longitude']   =   $location->longitude;

        $response['data'] = $temp;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to update pickup location details
  public function update_pickup_location_detail(Request $request)
  {
    if ($request->location_id) {

      $location = PickupLocation::where('id', $request->location_id)->first();

      if (!$location) {
        $response['success'] = false;
        $response['message'] = "Error! Pickup Location not found.";
      } else {

        $location->update([
          'location' => $request->location,
          'location_name' => $request->location_name,
          'latitude' => $request->latitude,
          'longitude' => $request->longitude
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Pickup Location Updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to delete pickup location details
  public function delete_pickup_location(Request $request)
  {
    if ($request->location_id) {

      $location = PickupLocation::where('id', $request->location_id)->first();

      if (!$location) {
        $response['success'] = false;
        $response['message'] = "Error! Pickup Location not found.";
      } else {

        $location->delete();

        $response['success'] = true;
        $response['message'] = "Success! Pickup location deleted successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to change pickup location status
  public function change_pickup_location_status(Request $request)
  {
    if ($request->location_id && ($request->is_active) != '') {

      $location = PickupLocation::where('id', $request->location_id)->first();

      if (!$location) {
        $response['success'] = false;
        $response['message'] = "Error! Location not found.";
      } else {
        $location->update([
          'is_active' => $request->is_active
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Pickup location status updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //////.................................... User Api Function Start ........................................////////


  //function used to get user details
  public function get_user_detail(Request $request)
  {
    if ($request->user_id) {
      $user = User::where('id', $request->user_id)->first();
      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! user not found.";
      } else {


        $response['success'] = true;
        $response['message'] = "Success! user found";
        $merchant = null;

        if ($user->merchant_assigned) {
          $merchant = User::select('id', 'merchant_name')->where('id', $user->merchant_assigned)->first();
        }

        $temp['id']          =   $user->id;
        $temp['first_name']  =   $user->first_name;
        $temp['last_name']   =   $user->last_name;
        $temp['phone']       =   $user->phone;
        $temp['email']       =   $user->email;
        $temp['is_payment_user']       =   $user->is_payment_user;
        $temp['merchant_assigned'] =   $user->merchant_assigned;
        $temp['merchant_name'] =   "";
        if ($merchant) {
          $temp['merchant_name'] =  $merchant->merchant_name;
        }
        $temp['photo']       =   Helper::imageUrl($user->getRawOriginal('photo'));
        $response['data'] = $temp;
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to update user details
  public function update_user_detail(Request $request)
  {
    if ($request->user_id) {

      $user = User::where('id', $request->user_id)->first();
      $check = User::where('email', $request->email)->where('user_type', 3)->where('id', '!=', $request->user_id)->count();
      if ($check == 0) {
        if ($request->photo) {
          $crop_image = $request->photo;
          list($type, $crop_image) = explode(';', $crop_image);
          list(, $crop_image)      = explode(',', $crop_image);
          $image = base64_decode($crop_image);

          $profile_img = 'user/img-' . time() . '.png';
          Helper::addS3Image($profile_img, $image);

          Helper::deleteS3Image($user->photo); //delete old s3 image if exists 
        } else {
          $profile_img = $user->getRawOriginal('photo');
        }

        $last_name = $request->last_name;
        if ($request->last_name == 'null') {
          $last_name = '';
        } else if (empty($request->last_name)) {
          $last_name = '';
        }


        $detail = [
          'first_name' => $request->first_name,
          'last_name' => $last_name,
          'phone' => $request->phone,
          'photo' => $profile_img,
          'email' => $request->email,
          'merchant_assigned' => $request->merchant_assigned,
        ];

        if($request->update_payment_user){
          $detail['is_payment_user'] = $request->is_payment_user;
        }

        $user->update($detail);

        $response['success'] = true;
        $response['message'] = "Success! User Updated successfully.";
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Email Id already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to add user details
  public function add_user(Request $request)
  {
    if ($request->first_name && $request->phone && $request->email) {
      $check = User::where('email', $request->email)->where('user_type', 3)->count();
      if ($check == 0) {
        if ($request->photo) {
          $crop_image = $request->photo;
          list($type, $crop_image) = explode(';', $crop_image);
          list(, $crop_image)      = explode(',', $crop_image);
          $image = base64_decode($crop_image);
          $profile_img = 'user/img-' . time() . '.png';

          Helper::addS3Image($profile_img, $image);
        } else {
          $profile_img = NULL;
        }

        $last_name = $request->last_name;
        if ($request->last_name == 'null') {
          $last_name = '';
        } else if (empty($request->last_name)) {
          $last_name = '';
        }

        $data = [
          'first_name' => $request->first_name,
          'last_name' => $last_name,
          'phone' => $request->phone,
          'photo' => $profile_img,
          'email' => $request->email,
          'password' => bcrypt($request->password),
          'merchant_assigned' => $request->merchant_assigned,
          'is_payment_user' => 0,
          'user_type' => 3
        ];

        if ($request->is_payment_user && intval($request->is_payment_user) == 1) {
          $data['is_payment_user'] = 1; // Payment User Role
        }

        $user = User::create($data); // craete user  
        $email = $request->email;
        $where_data   = array('email' => $email);

        $user_detail  = ApiModel::fetchSingleRecord('users', $where_data);

        $code = \Illuminate\Support\Str::random(64);
        \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(['email'=>$user_detail->email],['token'=>hash('sha256',$code),'created_at'=>now()]);
        $forgot_data  = array('token' => $code);
        ApiModel::editRecord('users', $where_data, $forgot_data);

        //Send mail to registered user for email verification.              
        $user_id = bcrypt($user_detail->id);
        $url = url('reset-password') .'/'. $code . '?signature=' . $user_id . '&type=' . $data['user_type'];
        $name = $user_detail->first_name;
        $today_date = date('M d, Y');
        $data = array('email' => $email, 'name' => $name, 'url' => $url, 'today_date' => $today_date, 'user_type' => $data['user_type'], 'is_payment_user' => $data['is_payment_user'], 'subject' => $data['is_payment_user'] == 1 ? 'fleetNG | Your Payment User Account has been Created' : 'fleetNG | Your User Account has been Created');

        Mail::send('mails.reset-password', $data, function ($message) use ($data) //Send Mail
        {
          $message->to($data['email'])->subject($data['subject']);
        });

        $response['success'] = true;
        $response['message'] = "Success! User added successfully.";
        $response['crediential'] = 'Email:-' . $request->email . '  Password:-' . $request->password;
        $response['login_pin'] = $user->auth_pin;
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Email Id already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to change user status
  public function change_user_status(Request $request)
  {
    if ($request->user_id && ($request->is_active) != '') {

      $user = User::where('id', $request->user_id)->first();

      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! user not found.";
      } else {
        $user->update([
          'is_active' => $request->is_active
        ]);

        $response['success'] = true;
        $response['message'] = "Success! User status updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to delete user details
  public function delete_user(Request $request)
  {
    if ($request->user_id) {

      $user = User::where('id', $request->user_id)->first();

      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! user not found.";
      } else {

        Helper::deleteS3Image($user->photo); //delete old s3 image if exists 

        $user->delete();

        $response['success'] = true;
        $response['message'] = "Success! User deleted successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //////.................................... Merchant Api Function Start ........................................////////

  //function used to get merchant details
  public function get_merchant_detail(Request $request)
  {
    if ($request->user_id) {
      $user = User::where('id', $request->user_id)->where('is_active', 1)->first();


      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! merchant not found.";
      } else {


        $response['success'] = true;
        $response['message'] = "Success! merchant found";

        $temp['id']          =   $user->id;
        $temp['merchant_id']          =   $user->merchant_id;
        $temp['merchant_name']  =   $user->merchant_name;
        $temp['merchant_address']   =   $user->merchant_address;
        $temp['phone']       =   $user->phone;
        $temp['email']       =   $user->email;
        $temp['first_name']       =   $user->first_name;
        $temp['last_name']       =   $user->last_name;
        $response['data'] = $temp;
      }
    } else if ($request->user_type) {
      $user = User::where('user_type', $request->user_type)->where('is_active', 1)->orderBy('id', 'asc')->get();
      $response['success'] = true;
      $response['message'] = "Success! merchant found";
      if (isset($user) && count($user) > 0) {
        foreach ($user as $value) {
          $temp['id']          =   $value->id;
          $temp['merchant_id']          =   $value->merchant_id;
          $temp['merchant_name']  =   $value->merchant_name;
          $temp['merchant_address']   =   $value->merchant_address;
          $temp['phone']       =   $value->phone;
          $temp['email']       =   $value->email;
          $response['data'][] = $temp;
        }
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Please enter the required field.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter the required field.";
    }

    return json_encode($response);
  }

  //function used to update merchant details
  public function update_merchant_detail(Request $request)
  {
    if ($request->user_id) {

      $user = User::where('id', $request->user_id)->first();
      $check = User::where('email', $request->email)->where('user_type', 4)->where('id', '!=', $request->user_id)->count();

      if ($check == 0) { //check if email exiest
        $last_name = $request->last_name;
        if ($request->last_name == 'null') {
          $last_name = '';
        } else if (empty($request->last_name)) {
          $last_name = '';
        }

        $user->update([
          'merchant_name' => $request->merchant_name,
          'merchant_address' => $request->merchant_address,
          'phone' => $request->phone,
          'first_name' => $request->first_name,
          'last_name' => $last_name,
          'email' => $request->email,
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Merchant Updated successfully.";
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Email Id already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to add merchant details
  public function add_merchant(Request $request)
  {
    if ($request->merchant_name && $request->phone && $request->email) {
      $check = User::where('email', $request->email)->where('user_type', 4)->count();

      if ($check == 0) {
        $MerchantIdCount = User::select('id')->where('user_type', 4)->orderBy('id', 'desc')->count();

        if (!empty($MerchantIdCount)) {
          $lastMerchantId = $MerchantIdCount + 1;
        } else {
          $lastMerchantId = 1;
        }

        //singlebyte strings
        $merchant_name = substr($request->merchant_name, 0, 3);
        $merchant_id = strtoupper($merchant_name) . "00" . $lastMerchantId;

        $last_name = $request->last_name;
        if ($request->last_name == 'null') {
          $last_name = '';
        } else if (empty($request->last_name)) {
          $last_name = '';
        }
        $user = User::create([
          'merchant_id' => $merchant_id,
          'merchant_name' => $request->merchant_name,
          'merchant_address' => $request->merchant_address,
          'phone' => $request->phone,
          'email' => $request->email,
          'first_name' => $request->first_name,
          'last_name' => $last_name,
          'password' => bcrypt($request->password),
          'user_type' => 4
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Merchant added successfully.";
        $response['login_pin'] = $user->auth_pin;
      } else {
        $response['success'] = false;
        $response['message'] = "Error! Email Id already exist. Please try again";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }

  //function used to change merchant status
  public function change_merchant_status(Request $request)
  {
    if ($request->user_id && ($request->is_active) != '') {

      $user = User::where('id', $request->user_id)->first();

      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! merchant not found.";
      } else {
        $user->update([
          'is_active' => $request->is_active
        ]);

        $response['success'] = true;
        $response['message'] = "Success! Merchant status updated successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }


  //function used to delete merchant details
  public function delete_merchant(Request $request)
  {
    if ($request->user_id) {

      $user = User::where('id', $request->user_id)->first();

      if (!$user) {
        $response['success'] = false;
        $response['message'] = "Error! merchant not found.";
      } else {

        if (!empty($user->photo)) {
          $path = public_path() . '/' . $user->photo;
          unlink($path);
        }

        $user->delete();

        $response['success'] = true;
        $response['message'] = "Success! Merchant deleted successfully.";
      }
    } else {
      $response['success'] = false;
      $response['message'] = "Error! Please enter all the required fields.";
    }

    return json_encode($response);
  }
}
