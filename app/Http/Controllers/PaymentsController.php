<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TripPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf as FacadePdf;
use Carbon\Carbon;

class PaymentsController extends Controller
{
    public function payment_list()
    {
        $user = Auth::user();

        if ($user->user_type === 2) {
            $payments = TripPayment::get()->each(function ($item) {
                $item->trip = $item->trip();
                $item->trip = $item->trip();
            });
        } else {
            $payments = TripPayment::whereIn('customer_id', $user->client_ids())->get()->each(function ($item) {
                $item->trip = $item->trip();
                $item->trip = $item->trip();
            });
        }

        return view('admin.payment-management.payment-list', compact('payments'));
    }
    public function trip_invoice_pdf_download($id)
    {
        $trip = Trip::where('id', $id)->first();
        $trip->client = $trip->client_info();
        $trip->pickup_location_alias = $trip->pickup_location_alias();
        $trip->drop_location = $trip->drop_location();
        $trip->total_trip_cost = $trip->total_trip_cost();

        if (!$trip) {
            return redirect()->back()->with('fail', 'Record not found');
        }

        $payment = TripPayment::where('trip_id', $id)->first();

        if (!$payment) {
            return redirect()->back()->with('fail', 'Invoice not found');
        }

        $data = ['payment' => $payment, 'trip' => $trip];
        $pdf = FacadePdf::loadView('admin.trip-management.trip-payment-invoice', $data);
        return $pdf->download('fleetng-trip-invoice-' . Carbon::now() . '.pdf');
    }
}
