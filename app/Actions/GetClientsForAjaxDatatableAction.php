<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Customer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;

class GetClientsForAjaxDatatableAction
{

    public static function getResponse(
        Collection $customers,
        Authenticatable $user,
        int $page,
        int $totalRecords,
        int $totalFilterRecords,
        string $orderByDirection,
        int $recordPerPage
    ) {

        $customers = self::getCustomer(
            $customers,
            $user,
            $page,
            $totalFilterRecords,
            $orderByDirection,
            $recordPerPage
        );

        return response()->json([
            'draw' => request('draw'),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $totalFilterRecords,
            'data' => $customers,
        ]);
    }

    public static function getCustomer(
        Collection $customers,
        Authenticatable $user,
        int $page,
        int $totalRecords,
        string $orderBy,
        int $recordPerPage
    ): array {
        $customerArray = [];

        if ($orderBy == 'desc') {
            $total = $totalRecords - (($page - 1) * $recordPerPage);
        } else {
            $total = (($page - 1) * $recordPerPage);
        }

        foreach ($customers as $key => $customer) {

            $actionBtn = self::getCustomerActionBtn($customer, $user);

            if ($orderBy == 'desc') {
                $index = $total - $key;
            } else {
                $index = $total  + ($key + 1);
            }

            $customerArray[] = [
                'DT_RowIndex' => $index,
                'full_name' =>  $customer->full_name,
                'phone_number' => $customer->country_code . ' ' . $customer->phone_number,
                'email' => $customer->email,
                'action' =>  $actionBtn,
            ];
        }

        return  $customerArray;
    }

    public static function getCustomerActionBtn(Customer $customer, Authenticatable $user): string
    {
        $actionBtn = '';

        if ($user->user_type === 1) {
            $actionBtn = '<a href="' . route('client-edit', $customer->id) . '" class="mr-1">
                <i class="fas fa-pen"></i>
            </a>';
            $actionBtn .= '<a href="' . route('client-status-change', $customer->id) . '" class="mr-1">';
            if ($customer->is_active) {
                $actionBtn .= '<i class="fas fa-unlock"></i>';
            } else {
                $actionBtn .= '<i class="fas fa-lock"></i>';
            }
            $actionBtn .= '</a>';
            $actionBtn .= '<a style="color: #7367f0;" class="mr-1" data-toggle="modal"
            onclick="setActionId(' . $customer->id . ')"
            data-target="#deleteSliderConfirm">
                <i class="fas fa-trash-alt"></i>
            </a>';
        }
        return $actionBtn;
    }
}
