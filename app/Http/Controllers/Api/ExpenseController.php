<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpenseController extends Controller
{
    public function expenses(Request $request)
    {
        $driverId = $request->driver_id;
        $driver = Driver::find($driverId);

        if (!$driver) {
            return response()->badRequest("Driver not found.");
        }

        $data = Expense::whereYear('created_at', date('Y'))->where('driver_id', $driverId)->orderBy('id', 'desc')->get();
        return response()->success('Data found.', $data);
    }

    public function single_expense(Request $request, $id)
    {
        $driverId = $request->driver_id;
        $driver = Driver::find($driverId);

        if (!$driver) {
            return response()->badRequest("Driver not found.");
        }

        $data = Expense::where(['id' => $id, 'driver_id' => $driverId])->first();
        if($data){
            return response()->success('Data found.', $data);
        }
        return response()->badRequest("Not found.");
    }

    public function add_expense(Request $request)
    {
        $driverId = $request->driver_id;
        $driver = Driver::find($driverId);
        if (!$driver) {
            return response()->badRequest("Driver not found.");
        }

        $rules = ['item_name' => 'required', 'item_cost' => 'required', 'item_quantity' => 'integer'];
        $message = ['item_name.*' => 'Please choose expense item.', 'item_cost.*' => 'Expense cost is required.', 'item_quantity.integer' => 'The item quantity must be a number.'];

        $validator = Validator::make($request->all(), $rules, $message);

        if ($validator->fails()) {
            return response()->badRequest($validator->errors()->first(), $validator->errors());
        }

        $data = [
            'item_name' => $request->item_name,
            'item_cost' => intval($request->item_cost),
            'item_quantity' => $request->item_quantity,
            'driver_id' => $driverId,
        ];

        $data = Expense::create($data);
        return response()->success('Expense added successfully.', $data);
    }
}
