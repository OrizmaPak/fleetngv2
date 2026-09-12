<?php

namespace Modules\Customers\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Customers\Entities\Checkout;
use Modules\Customers\Entities\TripPayment;
use Modules\Customers\Services\CheckoutService;

class PaymentController extends Controller
{
    public function tripPayment(Request $request, CheckoutService $service)
    {
        $checkout = null;
        try { $checkout = $service->verify($request->input('transaction_id')); }
        catch (\Throwable $error) { report($error); }
        $query = http_build_query(['reference' => $checkout ? $checkout->reference : '', 'status' => $checkout ? $checkout->status : 'unverified']);
        $destination = app()->environment(['local', 'testing']) ? url('customer-portal') . '#/payment-return?' : rtrim(config('customers.front_url'), '/') . '/trip/payments?';
        return redirect($destination . $query);
    }

    public function tripPaymentLink(Request $request, CheckoutService $service)
    {
        $data = $request->validate(['trip_ids' => 'required|array|min:1|max:100', 'trip_ids.*' => 'required|integer|distinct']);
        $checkout = $service->create($request->user(), $data['trip_ids']);
        // Preserve the existing gateway envelope for API clients.
        return response()->json(['status' => 'success', 'data' => ['link' => $checkout->checkout_url, 'reference' => $checkout->reference]]);
    }

    public function checkoutStatus(Request $request, $reference)
    {
        $checkout = Checkout::where('customer_id', $request->user()->id)->where('reference', $reference)->firstOrFail();
        return response()->success('', ['reference' => $checkout->reference, 'status' => $checkout->status, 'amount' => $checkout->amount_minor / 100, 'currency' => $checkout->currency]);
    }

    public function tripPayments(Request $request)
    {
        $transactions = TripPayment::where('customer_id', $request->user()->id)->get()->each(function ($item) { $item->trip = $item->trip(); });
        return response()->success('', $transactions);
    }
}
