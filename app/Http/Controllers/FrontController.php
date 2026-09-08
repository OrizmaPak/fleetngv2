<?php

namespace App\Http\Controllers;

use App\Mail\ContactUstMail;
use App\Models\Driver;
use App\Models\Page;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function refund_policy()
    {
        $data = Page::where('name', 'Refund Policy')->first();
        return view('front.refund-policy', compact('data'));
    }

    public function privacy_policy()
    {
        $data = Page::where('name', 'Privacy Policy')->first();
        return view('front.privacy-policy', compact('data'));
    }

    public function terms_conditions()
    {
        $data = Page::where('name', 'Terms & Conditions')->first();
        return view('front.terms-conditions', compact('data'));
    }
    public function contact_us()
    {
        return view('front.contact-us');
    }

    public function submit_contact_request(Request $request)
    {

        $validation_rule = [
            "name"    => "required|max:255",
            "email"     => "required|email|max:255",
            "phone"     => "required|regex:/^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/",
            "message"   => "max:1000",
        ];

        $valdation_message = [
            'name.required'    => 'Name field is required.',
            'name.max'         => 'Maximum 255 characters are allowed in the name.',

            'email.required'     => 'Email field is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.max'          => 'Maximum 255 characters are allowed in the email.',

            'phone.required'     => 'Phone field is required.',
            'phone.phone'        => 'Please enter a valid phone number.',

            'message.max'        => 'Maximum 1000 characters are allowed in the message.',
        ];

        $validator = Validator::make($request->all(), $validation_rule, $valdation_message);

        if ($validator->fails()) { //if validator fails then return 400 request error
            return response()->json([
                'status'    => false,
                'message'   => $validator->errors()->first(),
                'data'      => $validator->errors(),
            ], 400);
        }
        //Send mail
        $emails = ['support@fleetng.com'];
        Mail::to($emails)->send(new ContactUstMail($validator->validated()));
        return response()->json([
            'status'    => true,
            'message'   => "Thank you for contact us. We'll get to back to you shortly.",
            'data'      => [],
        ]);
    }

    public function send_test_notification()
    {
        $driver = Driver::where(['id' => 40])->whereNotNull('device_token')->select('device_token')->first();
        if ($driver && $driver->device_token) {
            $SERVER_API_KEY = config('app.fcm_server_key');

            $body = "Test notification.";
            $data = [
                "to" => $driver->device_token,
                "notification" => [
                    "title" => "New Trip Request",
                    "body" => $body,
                    'icon' => "https://pwa.fleetng.com/images/icons/dashboard-truck.png",
                    "click_action" => "/trip-requests",
                ],
            ];
            $dataString = json_encode($data);

            $headers = [
                'Authorization: key=' . $SERVER_API_KEY,
                'Content-Type: application/json',
            ];

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $dataString);

            $response = curl_exec($ch);
            $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            return response()->json(['status' => $status, 'response' => json_decode($response, true)], $status ?: 502);
        }

        return response()->json(['message' => 'Driver has no device token.'], 422);
    }
}
