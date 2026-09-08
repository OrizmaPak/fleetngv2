<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Trip;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Carbon\Carbon;

class GetTripsForAjaxDatatableAction
{

    public static function getResponse(
        Collection $trips,
        Authenticatable $user,
        int $page,
        int $totalRecords,
        int $totalFilterRecords,
        string $orderByDirection,
        int $recordPerPage,
        bool $isFilter = false
    ) {

        $trips = self::getTrips(
            $trips,
            $user,
            $page,
            $totalFilterRecords,
            $orderByDirection,
            $recordPerPage,
            $isFilter
        );

        return response()->json([
            'draw' => request('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilterRecords,
            'data' => $trips,
        ]);
    }

    public static function getTrips(
        Collection $trips,
        Authenticatable $user,
        int $page,
        int $totalRecords,
        string $orderBy,
        int $recordPerPage,
        bool $isFilter
    ): array {
        $tripsArray = [];

        if ($orderBy == 'desc') {
            $total = $totalRecords - ($page * $recordPerPage);
        } else {
            $total = (($page - 1) * $recordPerPage);
        }

        $total = $totalRecords;

        foreach ($trips as $key => $trip) {

            if (!$trip->payment_id && $user->is_payment_user && $trip->status == 3) {
                $cashCheckbox = '<input type="checkbox" class="bulk_payment_receive" data-id="' . $trip->id . '" data-total_cost="' . $trip->total_trip_cost() . '" onchange="handleBulkCheckbox(this)"/>';
            } else {
                $cashCheckbox = '<input type="checkbox" disabled />';
            }

            $status = self::getTripStatus($trip);
            $paymentStatus = self::getTripPaymentStatus($trip);
            $actionBtn = self::getTripActionBtn($trip, $user, $isFilter);

            if ($orderBy == 'desc') {
                $index = $total - $key;
            } else {
                $index = $total  + ($key + 1);
            }

            $tripsArray[] = [
                'DT_RowIndex' => $trip->id,
                'cash_checkbox' =>  $cashCheckbox,
                'name' =>  '<a href="' . route('view-driver', $trip->driver->id) . '" class="mr-1">' . $trip->driver->first_name . ' ' . $trip->driver->last_name . '</a>',
                'client_name' => $trip->client_name,
                'total_cost'  =>  number_format(($trip->total_cost ?? 0) + ($trip->cost_of_sand ?? 0) + ($trip->road_money ?? 0), 2),
                'driver_commission' =>  number_format($trip->driver_commission ?? 0, 2),
                'trip_generated_at' =>  Carbon::parse($trip->trip_generated_at ?? $trip->created_at)->format("d/m/Y"),
                'pickup'  =>   $trip->pickupLocation->location_name ?? (
                    $trip->pickupLocation->location ?? ''
                ),
                'drop_off' =>  $trip->dropLocation->location ?? '',
                'status'  =>  $status,
                'payment_status' =>  $paymentStatus,
                'action' =>  $actionBtn,
            ];
        }

        return  $tripsArray;
    }

    public static function getTripActionBtn(Trip $trip, Authenticatable $user, bool $isFilter): string
    {

        $cancel_trip = '';
        $share_btn = "";
        $copy_btn = "";

        if (!$user->is_payment_user && $isFilter) {
            $cancel_trip = '<a style="color: #7367f0;" class="mr-1" data-toggle="modal" onclick="get_delete_id(' . $trip->id . ')" data-target="#deleteSliderConfirm1"><i class="fas fa-trash-alt"></a>';
        }

        if (!$trip->payment_id && $trip->client_id && $user->is_payment_user && ($trip->status == 1 || $trip->status == 2 || $trip->status == 3)) {
            $share_btn = '<a href="javascript:void(0)" title="Share Payment Info" class="mr-1" onclick="shareTrip(this)" data-id="' . $trip->id . '" data-client-name="' . $trip->client_name . '" data-id="' . $trip->id . '" data-client-phone="' . $trip->client->phone_number . '"><i class="fas fa-bell"></i></a>';
            $copy_btn = '<a href="javascript:void(0)" title="Copy Link" class="mr-1"  onclick="copyTripLink(this)" data-id="' . $trip->id . '" data-client-name="' . $trip->client_name . '"><i class="fas fa-link"></i></a>';
        }

        return '<a href="' . route('trip-detail', $trip->id) . '" title="View" class="mr-1"><i class="fas fa-eye"></i></a>' . $cancel_trip . $share_btn . $copy_btn;
    }

    public static function getTripPaymentStatus(Trip $trip): string
    {
        if (($trip->status == 1 || $trip->status == 2) && !$trip->payment_id) {
            $paymentStatus = '<span>--</span>';
        } else if ($trip->status == 3 && !$trip->payment_id) {
            $paymentStatus = '<span class="badge badge-warning">Pending</span>';
        } else if ($trip->payment_confirm_by_bank_transfer) {
            $paymentStatus = '<span class="badge badge-success">Bank Transfer</span>';
        } else if ($trip->payment_confirmed_by_pos) {
            $paymentStatus = '<span class="badge badge-success">POS Payment</span>';
        } else if ($trip->payment_id) {
            $paymentStatus = '<span class="badge badge-success">Paid</span>';
        } else {
            $paymentStatus = '<span>--</span>';
        }

        return $paymentStatus;
    }

    public static function getTripStatus(Trip $trip): string
    {

        if ($trip->status == 1) {
            $status = 'New Trip';
        } else if ($trip->status == 2) {
            $status = '<a href="' . route('trip-live-location', $trip->id) . '"  class="badge badge-success"><u>Live Trip</u></a>';
        } else if ($trip->status == 3) {
            $status = 'Completed Trip';
        } else if ($trip->status == 4) {
            $status = '<span class="badge badge-danger">Canceled</span>';
        } else if ($trip->status == 5) {
            $status = '<span  class="badge badge-danger">Declined</span>';
        } else {
            $status = '--';
        }

        return $status;
    }
}
