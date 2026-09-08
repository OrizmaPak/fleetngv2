<?php

namespace Modules\Customers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\Customers\Entities\Customer;
use Modules\Customers\Entities\Trip;
use Modules\Customers\Entities\User;

class CustomerController extends Controller
{
    public function stats(Request $request)
    {
        try {
            $customer = $request->user(); // get customer data 
            $allTrips = Trip::select('id', 'payment_id', 'total_cost')->where(['client_id' => $customer->id])->get();
            $paid_trips = 0;
            $unpaid_trips = 0;
            $total_payment = 0;

            foreach($allTrips as $trip){
                 $total_payment = $total_payment+$trip->total_cost;

                if($trip->payment_id){
                    $paid_trips++;
                }else{
                    $unpaid_trips++;
                }
            }

            $data = [
                'all_trips' => count($allTrips),
                'completed_trips' => Trip::where(['client_id' => $customer->id, 'status' => 3])->count(),
                'canceled_trips' => Trip::where(['client_id' => $customer->id, 'status' => 4])->count(),
                'declined_trips' => Trip::where(['client_id' => $customer->id, 'status' => 5])->count(),
                'paid_trips' => $paid_trips,
                'unpaid_trips' => $unpaid_trips,
                'total_payment' => $total_payment,
            ];

            return response()->success("", $data);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function profile(Request $request)
    {
        try {
            $customer = $request->user(); // get customer data 
            return response()->success("", $customer);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function profileUpdate(Request $request){
        try {
            $customer = $request->user(); // get customer data 

            $validate = [
                'phone_number' => 'required|regex:/^\\d+$/|min:9|max:13',
                'email' => 'required|email',
                'first_name' => 'required',
            ];

            $validator = Validator::make($request->all(), $validate, ['phone_number.*' => 'Invalid phone number.']);  // validate the request data

            if ($validator->fails()) {
                return response()->badRequest($validator->errors()->first(), $validator->errors());
            }

            // check phone number exists
            if($customer->phone_number !== $request->phone_number && Customer::where('phone_number', $request->phone_number)->exists()){
                return response()->badRequest("Phone number has already been taken", ['phone_number' => 'Phone number has already been taken']);
            }

            // check email exists
            if($customer->email !== $request->email && Customer::where('email', $request->email)->exists()){
                return response()->badRequest("Email has already been taken", ['email' => 'Email has already been taken']);
            }

            $customer->first_name = $request->first_name;
            $data = [
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
                "phone_number" => $request->phone_number,
                "email" => $request->email,
            ];

            if($request->country_code){
                $data['country_code'] = $request->country_code;
            }

            if ($request->file('profile_image')) { // if customer uploads the profile image
                // delete old image if exists
                if (!empty($customer->profile_image) && Storage::exists($customer->profile_image)) {
                    Storage::delete($customer->profile_image);
                }
    
                $file                  = $request->file('profile_image');
                $data['profile_image'] = 'customers/profile/' . md5(time()) . '.' . $file->getClientOriginalExtension();
                Storage::disk('s3')->put($data['profile_image'], file_get_contents($file)); //store new image in storage
                $storage_url = Storage::disk('s3')->url($data['profile_image']); // get stored image url of s3
                $data['profile_image'] = $storage_url;
            }

            $customer->update($data);
            return response()->success("Profile updated successfully.", $customer);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        } 
    }

    public function saveDeviceToken(Request $request){
        try {
            $customer = $request->user(); // get customer data 
            Customer::where("id", $customer->id)->update(["device_token" => $request->device_token]);
            return response()->success("Device token saved successfully!", $request->device_token);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function merchants()
    {
        try {
            $merchants = User::select('id', 'merchant_name')->where(['user_type' => 4, 'is_active' => 1])->get(); // get merchants data with their Drivers

            $merchants = $merchants->map(function ($item) {
                if (!count($item->drivers())) {
                    return null;
                }
                return ['id' => $item->id, 'name' => $item->merchant_name, 'drivers' => $item->drivers()];
            });

            $merchants = array_filter($merchants->toArray());  // filter merchant data 
            return response()->success("", $merchants);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }
}
