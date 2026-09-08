<?php

namespace Modules\Customers\Http\Controllers\Api;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\Customers\Entities\Customer;

// use Modules\Customers\Entities\Customer;
// use Modules\Customers\Entities\User;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('customers::index');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create(Request $request)
    {
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {

        $validate = [
            'first_name' => 'required',
            'email' => 'required|email|unique:customers,email',
            'phone_number' => 'required|regex:/^\\d+$/|min:9|max:13|unique:customers,phone_number',
        ];

        $message = [
            'phone_number.regex' => 'Invalid phone number',
            'phone_number.min' => 'Invalid phone number',
            'phone_number.max' => 'Invalid phone number'
        ];
        $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

        if ($validator->fails()) {
            return response()->badRequest($validator->errors()->first(), $validator->errors());
        }

        $data = [
            "first_name" => $request->first_name,
            "last_name" => $request->last_name,
            "phone_number" => $request->phone_number,
            "country_code" => $request->country_code,
            "email" => $request->email
        ];
        $customer = Customer::create($data);  // create customer data in database
        $customer->access_token = $customer->createToken("API_TOKEN")->plainTextToken; // generate access token
        return response()->success("Phone number verified successfully.", $customer);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('customers::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('customers::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    public function login(Request $request)
    {
        $validate = [
            'phone_number' => 'required|regex:/^\\d+$/|min:9|max:13|exists:customers,phone_number',
        ];

        $message = [
            'phone_number.regex' => 'Invalid phone number',
            'phone_number.min' => 'Invalid phone number',
            'phone_number.max' => 'Invalid phone number'
        ];
        $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

        if ($validator->fails()) {
            return response()->badRequest($validator->errors()->first(), $validator->errors());
        }

        $customer = Customer::where('phone_number', $request->phone_number)->first();
        if (!$customer) {
            return response()->badRequest("Data not found");
        }

        $customer->tokens()->delete(); //delete all old tokens fron database
        $customer->access_token = $customer->createToken("API_TOKEN")->plainTextToken; // generate access token

        return response()->success("Phone number verified successfully.", $customer);
    }

    public function verifyPhone(Request $request)
    {
        $validate = [
            'phone_number' => 'required|regex:/^\\d+$/|min:9|max:13',
        ];

        $message = [
            'phone_number.regex' => 'Invalid phone number',
            'phone_number.min' => 'Invalid phone number',
            'phone_number.max' => 'Invalid phone number'
        ];
        $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

        if ($validator->fails()) {
            return response()->badRequest($validator->errors()->first(), $validator->errors());
        }

        $customer = Customer::where('phone_number', $request->phone_number)->exists();
        $result = ['details' => false];
        if ($customer) {
            $result['details'] = true;
        }

        return response()->success("", $result);
    }

    public function verifyDetails(Request $request)
    {
        $validate = [
            'phone_number' => 'required|regex:/^\\d+$/|min:9|max:13',
            'email' => 'required|email',
        ];

        $message = [
            'phone_number.regex' => 'Invalid phone number',
            'phone_number.min' => 'Invalid phone number',
            'phone_number.max' => 'Invalid phone number'
        ];

        $validator = Validator::make($request->all(), $validate, $message);  // validate the request data

        if ($validator->fails()) {
            return response()->badRequest($validator->errors()->first(), $validator->errors());
        }

        $phone = Customer::where('phone_number', $request->phone_number)->exists();
        $email = Customer::where('email', $request->email)->exists();
        $result = ['phone_number' => $phone, 'email' => $email];
        return response()->success("", $result);
    }
}
