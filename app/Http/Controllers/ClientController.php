<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Actions\GetClientsForAjaxDatatableAction;

class ClientController extends Controller
{

    public function client_list()
    {
        return view('admin.client-management.client-list');
    }

    // this function is used by ajax call to show client list detail
    public function client_list_detail(Request $request)
    {

        abort_if(!$request->ajax(), 400);

        $user = Auth::user();

        $searchKey = $request->input('search.value');
        $recordPerPage = (int) $request->input('length', 10);
        $start = (int) $request->input('start');
        $page = ($start + $recordPerPage) / $recordPerPage;
        $page = $page < 1 ? 1 : $page;

        if ((int) $user->user_type === 1) {
            $query = Customer::query();
        } else {
            $query = Customer::whereIn('id', $user->client_ids());
        }

        if ($request->input('format') === 'modern') {
            return $this->modernClientList($request, $query, $user);
        }

        $totalRecords = $query->count();

        $clients = $query->clone()
            ->where(function ($subQuery) use ($searchKey) {
                if (!empty($searchKey)) {

                    $subQuery->where('first_name', 'like', '%' . $searchKey . '%')
                        ->orwhere('last_name', 'like', '%' . $searchKey . '%')
                        ->orwhere('email', 'like', '%' . $searchKey . '%')
                        ->orwhere('phone_number', 'like', '%' . $searchKey . '%');
                }
            });

        $filterRecords = $clients->count();

        $clients = $clients->take($recordPerPage)
            ->skip($start)
            ->orderBy('id', $request->input('order.0.dir', 'asc'))
            ->get();


        return GetClientsForAjaxDatatableAction::getResponse(
            $clients,
            $user,
            $page,
            $totalRecords,
            $filterRecords,
            $request->input('order.0.dir', 'asc'),
            $recordPerPage
        );
    }

    private function modernClientList(Request $request, $query, $user)
    {
        $validated = $request->validate([
            'page' => ['nullable', 'integer', 'min:1'],
            'per_page' => ['nullable', 'integer', 'in:10,25,50,100'],
            'sort' => ['nullable', 'in:id,first_name,phone_number,email,is_active'],
            'direction' => ['nullable', 'in:asc,desc'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $page = (int) ($validated['page'] ?? 1);
        $perPage = (int) ($validated['per_page'] ?? 10);
        $sort = $validated['sort'] ?? 'id';
        $direction = $validated['direction'] ?? 'desc';
        $search = trim($validated['search'] ?? '');
        $total = (clone $query)->count();

        if ($search !== '') {
            $query->where(function ($subQuery) use ($search) {
                $subQuery->where('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone_number', 'like', '%' . $search . '%');
            });
        }

        $filtered = (clone $query)->count();
        $lastPage = max(1, (int) ceil($filtered / $perPage));
        $page = min($page, $lastPage);
        $clients = $query->orderBy($sort, $direction)
            ->forPage($page, $perPage)
            ->get(['id', 'first_name', 'last_name', 'country_code', 'phone_number', 'email', 'is_active']);

        return response()->json([
            'data' => $clients->map(function ($client) use ($user) {
                $actions = [];
                if ((int) $user->user_type === 1) {
                    $actions = [
                        ['label' => 'Edit', 'url' => route('client-edit', $client->id)],
                        ['label' => $client->is_active ? 'Deactivate' : 'Activate', 'url' => route('client-status-change', $client->id)],
                        ['label' => 'Delete', 'action' => 'delete-client', 'value' => $client->id, 'danger' => true],
                    ];
                }

                return [
                    'id' => $client->id,
                    'full_name' => trim($client->first_name . ' ' . $client->last_name),
                    'phone_number' => trim(($client->country_code ?: '') . ' ' . $client->phone_number),
                    'email' => $client->email,
                    'status' => $client->is_active ? 'Active' : 'Inactive',
                    'actions' => $actions,
                ];
            })->values(),
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $filtered,
                'unfiltered_total' => $total,
                'last_page' => $lastPage,
                'sort' => $sort,
                'direction' => $direction,
            ],
        ]);
    }

    public function client_edit(Request $request, $id)
    {
        if(Auth::user()->user_type !== 1){ 
            return redirect()->back()->with('fail', "Something went wrong!");  // validate the ADMIN role
        }

        $client = Customer::where('id', $id)->first();

        if($request->isMethod('post')){
            $validate = [
                'phone_number' => 'required|regex:/^\\d+$/|min:9|max:13',
                'email' => 'required|email',
                'first_name' => 'required',
            ];

            $validator = Validator::make($request->all(), $validate, ['phone_number.*' => 'Invalid phone number.']);  // validate the request data

            if ($validator->fails()) {
                return redirect()->back()->with('fail', $validator->errors()->first());
            }

            // check phone number exists
            if($client->phone_number !== $request->phone_number && Customer::where('phone_number', $request->phone_number)->exists()){
                return redirect()->back()->with('fail', "Phone number has already been taken");
            }

            // check email exists
            if($client->email !== $request->email && Customer::where('email', $request->email)->exists()){
                return redirect()->back()->with('fail', "Email has already been taken");
            }

            $data = [
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ];

            Customer::where('id', $id)->update($data);

            return redirect(route('client-list'))->with('success', "Client detail updated successfully!");
        }

        return view('admin.client-management.client-edit', compact('client'));
    }

    public function client_status_change($id)
    {
        abort_unless((int) Auth::user()->user_type === 1, 403);

        $client = Customer::where('id', $id)->first();
        if ($client) {
            $client->is_active = $client->is_active ? 0 : 1;
            $client->save();
            return redirect(route('client-list'))->with('success', "Client status updated successfully!");
        } else {
            return redirect(route('client-list'))->with('fail', "Something went wrong!");
        }
    }

    //function used to delete driver 
    public function client_delete(Request $request)
    {
        abort_unless((int) Auth::user()->user_type === 1, 403);

        $client_delete = Customer::where('id', $request->client_id)->delete();
        if ($client_delete) {
            return redirect(route('client-list'))->with('success', "Client deleted successfully!");
        } else {
            return redirect(route('client-list'))->with('fail', "Something went wrong!");
        }
    }
}
