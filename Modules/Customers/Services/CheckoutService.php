<?php

namespace Modules\Customers\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Modules\Customers\Entities\Checkout;
use Modules\Customers\Entities\Trip;
use Modules\Customers\Entities\TripPayment;

class CheckoutService
{
    public static function minor($amount)
    {
        if (!is_numeric($amount) || !is_finite((float) $amount) || $amount < 0 || $amount > 1000000000) {
            throw ValidationException::withMessages(['amount' => 'Invalid payment amount.']);
        }
        return (int) round((float) $amount * 100);
    }

    private function gateway()
    {
        abort_unless(config('customers.payments_enabled') && config('app.flutterwave_secret_key'), 503, 'Payment checkout is not configured. No payment has been taken.');
        return Http::withToken(config('app.flutterwave_secret_key'))->acceptJson()->timeout(20)->withOptions(['connect_timeout' => 5]);
    }

    public function create($customer, array $ids)
    {
        $gateway = $this->gateway();
        $checkout = DB::transaction(function () use ($customer, $ids) {
            $trips = Trip::where('client_id', $customer->id)->whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get();
            abort_unless($trips->count() === count($ids), 404);
            $amounts = [];
            foreach ($trips as $trip) {
                if ($trip->payment_id || in_array((int) $trip->status, [4, 5], true)) {
                    throw ValidationException::withMessages(['trip_ids' => 'Some selected trips are already paid or cannot be paid. Refresh your trips.']);
                }
                $amounts[$trip->id] = self::minor($trip->total_trip_cost());
            }
            if (array_sum($amounts) <= 0) throw ValidationException::withMessages(['trip_ids' => 'There is no outstanding amount for this selection.']);
            return Checkout::create(['reference' => 'fleetng-' . Str::uuid(), 'customer_id' => $customer->id, 'trip_amounts' => $amounts, 'amount_minor' => array_sum($amounts), 'currency' => 'NGN']);
        });
        try {
            $response = $gateway->post('https://api.flutterwave.com/v3/payments', [
                'tx_ref' => $checkout->reference, 'amount' => $checkout->amount_minor / 100, 'currency' => 'NGN',
                'redirect_url' => url('customer/trips/payments'),
                'customer' => ['email' => $customer->email, 'phone_number' => $customer->phone_number, 'name' => $customer->full_name],
                'customizations' => ['title' => 'FleetNG', 'description' => 'Trip payment'],
            ])->throw()->json();
            $link = $response['data']['link'] ?? '';
            if (($response['status'] ?? '') !== 'success' || !$this->validLink($link)) throw new \RuntimeException('Invalid checkout response.');
            $checkout->update(['checkout_url' => $link]);
            return $checkout;
        } catch (\Throwable $error) {
            $checkout->update(['status' => 'failed']);
            throw ValidationException::withMessages(['payment' => 'Checkout could not be opened. Please try again.']);
        }
    }

    public function validLink($link)
    {
        $host = strtolower((string) parse_url($link, PHP_URL_HOST));
        return parse_url($link, PHP_URL_SCHEME) === 'https' && ($host === 'flutterwave.com' || Str::endsWith($host, '.flutterwave.com'));
    }

    public function verify($transactionId)
    {
        if (!preg_match('/^\d{1,20}$/', (string) $transactionId)) return null;
        $response = $this->gateway()->get('https://api.flutterwave.com/v3/transactions/' . $transactionId . '/verify')->throw()->json();
        $data = $response['data'] ?? [];
        if (($response['status'] ?? '') !== 'success' || (string) ($data['id'] ?? '') !== (string) $transactionId) return null;
        return DB::transaction(function () use ($data, $transactionId) {
            $checkout = Checkout::where('reference', $data['tx_ref'] ?? '')->lockForUpdate()->first();
            if (!$checkout) return null;
            if ($checkout->status === 'successful') return (string) $checkout->transaction_id === (string) $transactionId ? $checkout : null;
            if (($data['status'] ?? '') !== 'successful') return $checkout;
            // Exact matching deliberately holds overpayments for reconciliation as well as underpayments.
            if (($data['currency'] ?? '') !== $checkout->currency || self::minor($data['amount'] ?? -1) !== $checkout->amount_minor) return null;
            if (Checkout::where('transaction_id', (string) $transactionId)->where('id', '!=', $checkout->id)->exists()) return null;
            $amounts = $checkout->trip_amounts;
            $trips = Trip::where('client_id', $checkout->customer_id)->whereIn('id', array_keys($amounts))->orderBy('id')->lockForUpdate()->get();
            if ($trips->count() !== count($amounts)) return null;
            foreach ($trips as $trip) {
                if ($trip->payment_id || in_array((int) $trip->status, [4, 5], true) || self::minor($trip->total_trip_cost()) !== (int) $amounts[$trip->id]) return null;
            }
            foreach ($trips as $trip) {
                $payment = TripPayment::create(['trip_id' => $trip->id, 'customer_id' => $checkout->customer_id, 'status' => 'successful', 'transaction_id' => (string) $transactionId, 'amount' => $amounts[$trip->id] / 100, 'payment_type' => substr((string) ($data['payment_type'] ?? 'gateway'), 0, 50), 'reference_code' => $checkout->reference]);
                $trip->update(['payment_id' => $payment->id]);
            }
            $checkout->update(['status' => 'successful', 'transaction_id' => (string) $transactionId]);
            return $checkout;
        });
    }
}
