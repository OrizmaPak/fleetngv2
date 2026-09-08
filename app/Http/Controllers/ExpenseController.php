<?php

namespace App\Http\Controllers;

use App\Models\ApiModel;
use App\Models\Driver;
use App\Models\Expense;
use App\Traits\AnalyticsTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ExpenseController extends Controller
{
    use AnalyticsTrait;
    public function expenses(Request $request)
    {

        $user = Auth::user();
        $condition = ['user_id' => $user->id];

        $drivers = Driver::select('id', 'first_name', 'last_name')->where($condition)->get();

        return view('admin.expense-manangement.expense-list', compact('drivers'));
    }

    public function expenses_list(Request $request)
    {
        $daterange = $request->daterange;
        $data = ApiModel::getExpenseData($daterange, $request->item_name, $request->phone,  $request->driver_id);

        if ($request->ajax()) {
            $total_expenses = count($data) ? collect($data)->sum('item_cost') : 0;
         
            return DataTables::of($data)
                ->with([
                    'total_expenses' => $total_expenses,
                ])
                ->addIndexColumn()
                ->addColumn('created_at', function ($data) {
                    return $data->created_at;
                })
                ->addColumn('name', function ($data) {
                    return $data->first_name . ' ' . $data->last_name;
                })
                ->addColumn('phone_number', function ($data) {
                    return $data->phone;
                })
                ->addColumn('item_name', function ($data) {
                    return $data->item_name;
                })
                ->addColumn('item_quantity', function ($data) {
                    return $data->item_quantity;
                })
                ->addColumn('item_cost', function ($data) {
                    return $data->item_cost;
                })
                ->addColumn('action', function ($data) {
                    $btn = '<a href="javascript:void(0)" class="mr-1" id="edit-expense-' . $data->id . '" onclick="editExpense(' . $data->id . ')"  data-name="' . $data->item_name . '" data-quantity="' . $data->item_quantity . '" data-cost="' . $data->item_cost . '"><i class="fas fa-pen"></i></a>';
                    $btn .= '<a href="javascript:void(0)" style="color: #7367f0;" data-toggle="modal" onclick="deleteAction(' . $data->id . ')" data-target="#deleteExpenseConfirm" class="mr-1"> <i class="fas fa-trash-alt"></i></a>';
                    return $btn;
                })
                ->rawColumns(['created_at', 'name', 'phone_number', 'item_name', 'item_quantity', 'item_cost', 'action'])
                ->make(true);
        }
    }


    public function delete_expense(Request $request)
    {
        $id = $request->expense_id;
        $expense = Expense::find($id);
        $userId = Auth::id();

        if ($expense) {
            $driver = Driver::find($expense->driver_id);
            if ($driver && $driver->merchant_id === $userId) {
                Expense::where(['id' => $id])->delete();
                return redirect()->back()->with('success', 'Expense deleted successfully.');
            }
        }
        return redirect()->back()->with('fail', 'Expense not found.');
    }

    public function edit_expense(Request $request)
    {
        $id = $request->expense_id;
        $expense = Expense::find($id);
        $user = Auth::user();

        if ($expense) {
            $driver = Driver::find($expense->driver_id);
            if ($driver && $driver->user_id === $user->id) {
                $data = [
                    'item_name' => $request->item_name,
                    'item_cost' => $request->item_cost,
                    'item_quantity' => $request->item_quantity,
                ];
                $expense->update($data);
                return redirect()->back()->with('success', 'Expense updated successfully.');
            }
        }
        return redirect()->back()->with('fail', 'Expense not found.');
    }
}
