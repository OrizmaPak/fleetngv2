<?php

namespace Modules\Customers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Modules\Customers\Entities\TripPayment;
use Modules\Customers\Entities\Trip;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function tripPayment(Request $request)
    {
        $front_url = Config::get('customers.front_url'); // get frontend url from config
        $secret_key =  Config::get('app.flutterwave_secret_key'); // get secret key from config
        $transaction_id = $request->transaction_id;
        $redirectUrl = $front_url . '/trip/payments?status=failed';

        $headers = [
            'Authorization' => 'Bearer ' . $secret_key,
            'Content-Type' => 'application/json',
        ];

        if($transaction_id){
            try {
                // get transaction details from api.flutterwave.com;
                $response = Http::withHeaders($headers)->get('https://api.flutterwave.com/v3/transactions/' . $transaction_id . '/verify');
                $response = $response->json();
                $status = $response['data']['status'];
                $payment_type = $response['data']['payment_type'];
                $reference_code = $response['data']['tx_ref'];
            } catch (\Throwable $th) {
                $status = 'N/A';
                $payment_type = Null;
                $reference_code = Null;
            }

            if($reference_code){
                $tripsIds = json_decode(base64_decode($reference_code));

                foreach($tripsIds as $key => $tripId){
                    if($key){
                        $trip = Trip::find($tripId);
    
                        $data = [
                            "trip_id" => $tripId,
                            "customer_id" => $trip ? $trip->client_id : Null,
                            "status" => $status,
                            "transaction_id" => $transaction_id,
                            "amount" => $trip ? $trip->total_trip_cost() : 0,
                            "payment_type" => $payment_type,
                            "reference_code" => $reference_code
                        ];
        
                        $payment = TripPayment::create($data);  // save transaction detail in database
        
                        if ($status === 'successful' && $trip) {
                            $trip->payment_id = $payment->id;  // save payment id in trips table
                            $trip->save();
                        }
                    }
                }
                $redirectUrl = $front_url . '/trip/payments?reference_code='. $reference_code .'&status=' . $status . '&transaction_id=' . $transaction_id;
            }
        }

        return redirect($redirectUrl); // redirect to payment failed page
    }

    public function tripPaymentLink(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), ['trip_ids' => 'required|array|exists:trips,id']); 

            if($validator->fails()){
                return response()->badRequest($validator->errors()->first(), $validator->errors());
            }

            $user = $request->user(); // get customer data 

            $trip_ids = [strtotime("now")];
            $amount = 0;
            foreach($request->trip_ids as $trip_id){
                $trip = Trip::find($trip_id);
                if(!$trip->payment_id){
                    $trip_ids[] = $trip_id;
                    $amount += $trip->total_trip_cost();
                }
            }
                        
            if (count($trip_ids)) {
                $referenceId = base64_encode(json_encode($trip_ids));
                $secret_key =  Config::get('app.flutterwave_secret_key'); // get secret key from config

                $headers = [
                    'Authorization' => 'Bearer ' . $secret_key,
                    'Content-Type' => 'application/json',
                ];

                $data = [
                    "tx_ref" =>  $referenceId,
                    "amount" =>  $amount,
                    "currency" =>  'NGN',
                    "payment_options" =>  'card,mobilemoney,ussd,googlepay',
                    "redirect_url" =>  url('customer/trips/payments'),
                    "customer" =>  [
                        "email" =>  $user->email,
                        "phone_number" =>  $user->phone_number,
                        "name" =>  $user->full_name,
                    ],
                    "customizations" =>  [
                        "title" =>  'FleetNG',
                        "description" =>  'Payment of your Trip',
                        "logo" =>  'https://st2.depositphotos.com/4403291/7418/v/450/depositphotos_74189661-stock-illustration-online-shop-log.jpg',
                    ],
                ];

                try {
                    $response = Http::withHeaders($headers)->post('https://api.flutterwave.com/v3/payments', $data);  // payment link
                    $response = $response->json();

                    if (isset($response['errors']) && $response['errors'][0]) {
                        return response()->badRequest($response['message']);
                    }

                    if (isset($response['status']) && $response['status'] !== 'success') {
                        return response()->badRequest($response['message']);
                    }

                    return $response;
                } catch (\Throwable $th) {
                    return response()->badRequest("Something went wrong.");
                }

            } else {
                return response()->badRequest("Payment already done.");
            }
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }

    public function tripPayments(Request $request)
    {
        try {
            $user = $request->user();
            $transactions = TripPayment::where('customer_id', $user->id)->get()->each(function ($item) {
                $item->trip = $item->trip();
            }); // get payment transtions data
            return response()->success("", $transactions);
        } catch (\Throwable $th) {
            return response()->failedRequest($th);
        }
    }
}
