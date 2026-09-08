<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\SmsLog;
use App\Models\Trip;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

class SMSController extends Controller
{

    public function send_sms_payment_alert_to_client(Request $request)
    {
        $trip = Trip::find($request->trip_id);
        $phone_number = "";
        if ($trip && $trip->client_id) {
            $client = Customer::find($trip->client_id);
            if ($client && $client->phone_number) {
                $phone_number = $client->phone_number;
            }
        }

        if (!$phone_number || !$request->sms_text || !Auth::user()->is_payment_user) {
            return redirect(route('trip-list'))->with('success', 'Something went wrong, please try again!');
        }

        $message = $request->sms_text;
        $phone_number = ($client->country_code ? $client->country_code : '234') . ' ' . $phone_number;
        $phone_number = str_replace(['+', ' '], '', $phone_number);
        
        $log = [
            "to" => $phone_number,
            "body" => $message,
            "type" => 'trip-payment-alert',
        ];


        try {
            // termii sms request
            $data = [
                'to' => $phone_number, 
                "from" => "N-Alert", 
                // "channel" => "generic", 
                "channel" => "dnd", 
                "type" => "plain", 
                'sms' => $message, 
                "api_key" => 'TLjfB1ZoqOahpt7kbZWUprumklt4s1DgZFAizpX2RrKGMJzY3W99h46xJPbDpt'
            ];

            $response = Http::post('https://api.ng.termii.com/api/sms/send', $data)->json();
            if ($response && isset($response['message_id']) && $response['message'] == "Successfully Sent") {
                $log['sms_id'] = $response['message_id'];
                $log['is_sent'] = 1;
            } else {
                if ($response && isset($response['message'])) {
                    $log['error_message'] = $response['message'];
                }
                SmsLog::create($log);
                return redirect(route('trip-list'))->with('fail', 'Something went wrong, please try again!');
            }

            return redirect(route('trip-list'))->with('success', 'Payment alert sms sent successfully!');
        } catch (Exception $e) {
            $log['error_message'] = $e->getMessage();
            SmsLog::create($log);
            return redirect(route('trip-list'))->with('fail', 'Something went wrong, please try again!');
        }
    }
    public function send_sms_payment_alert_to_client_twilio(Request $request)
    {
        $trip = Trip::find($request->trip_id);
        $phone_number = "";
        if ($trip && $trip->client_id) {
            $client = Customer::find($trip->client_id);
            if ($client && $client->phone_number) {
                $phone_number = $client->phone_number;
            }
        }

        if (!$phone_number || !$request->sms_text || !Auth::user()->is_payment_user) {
            return redirect(route('trip-list'))->with('success', 'Something went wrong, please try again!');
        }

        $account_sid = config('twilio.sid');
        $auth_token = config('twilio.auth_token');
        $twilio_number = config('twilio.number');
        $message = $request->sms_text;
        $twilio = new Client($account_sid, $auth_token);
        $phone_number = ($client->country_code ?? '+234') . $phone_number;

        $data = [
            "from" => $twilio_number,
            "to" => $phone_number,
            "body" => $message,
            "account_id" => $account_sid,
            "type" => 'trip-payment-alert',
        ];

        try {
            // twilio sms request
            $response = $twilio->messages->create(
                $phone_number,
                ['from' => $twilio_number, 'body' => $message]
            );

            $response = $response->toArray();

            if ($response['errorCode']) {
                SmsLog::create($data);
                return response(["status" => false, "message" => "Send SMS request failed.", "data" => null]);
            }

            $data['sms_id'] = $response['sid'];
            $data["is_sent"] = 1;

            SmsLog::create($data);
            return redirect(route('trip-list'))->with('success', 'Payment alert sms sent successfully!');
        } catch (Exception $e) {
            $data['error_message'] = $e->getMessage();
            SmsLog::create($data);
            return redirect(route('trip-list'))->with('success', 'Something went wrong, please try again!');
        }
    }
}
