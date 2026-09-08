<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ApiModel extends Model
{
	use HasFactory;

	//This function is used for fetch single record from a table.
	public static function fetchSingleRecord($table, $data)
	{
		$response = DB::table($table)
			->where($data)
			->orderBy('id', 'desc')
			->first();

		return $response;
	}


	//This function is used for check record is available in table or not.
	public static function checkSingleRecord($table, $data)
	{
		$response = DB::table($table)
			->where($data)
			->first();

		return $response;
	}

	//This function is used for update single record.
	public static function editRecord($table, $where_data, $data)
	{
		$response = DB::table($table)
			->where($where_data)
			->update($data);

		return $response;
	}

	//This function is used for update single record.
	public static function updateSingleRecords($table, $id, $data)
	{
		$response = DB::table($table)
			->where('id', $id)
			->update($data);
		return $response;
	}

	public static function fetchSelectedTags($id)
	{
		$response = DB::table('blog_tags')
			->leftjoin('tags', 'blog_tags.tag_id', '=', 'tags.id')
			->select('tags.id as tag_id', 'tags.title as tag_name')
			->where('blog_tags.blog_id', $id)
			->get();

		return $response;
	}


	public static function fetchAllTrips($trip_type, $driver, $location, $daterange)
	{

		if ($daterange == null || $daterange == '') {
			$created_at = '';
			$from_date = '';
			$to_date = '';
		} else {
			$date = explode('to', $daterange);
			if (count($date) == 1) {
				$created_at = Carbon::parse($date[0])->toDateTimeString();
				$from_date = '';
				$to_date = '';
			} else {
				$created_at = '';
				$from_date = Carbon::parse($date[0])->toDateTimeString();
				$to_date = Carbon::parse($date[1])->setTime(23, 59, 59)->toDateTimeString();
			}
		}

		$user = Auth::user();

		if (session('user_role') == 1) {
			$driver_id = Driver::pluck('id');
		} else {
			if ($user->user_type == 2) { //admin
				$driver_id =  Driver::pluck('id');
			} else { //merchant and user
				$driver_id =  Driver::where('user_id', $user->id)->pluck('id');
				if ($user->is_payment_user && $user->merchant_assigned) {
					$driver_id =  Driver::where('merchant_id', $user->merchant_assigned)->pluck('id'); //to show all merchants trip 
				} else {
					$driver_id =  Driver::where('user_id', $user->id)->pluck('id');
				}
			}
		}

		$statusCode = [1, 2, 3, 4, 5]; // UPDATE ON 15 JUNE 2024 - Need to show new trips for user so he can have ability to delete a mistake made at trip creation level

		$response = DB::table('trips')
			->select('drivers.first_name', 'drivers.last_name', 'customers.id', 'customers.phone_number as client_phone_number', 'trips.driver_id', 'trips.total_cost', 'trips.cost_of_sand', 'trips.road_money', 'trips.client_name', 'trips.client_id', 'trips.payment_id', 'trips.payment_confirm_by_bank_transfer', 'trips.driver_commission', 'trips.trip_generated_at', 'trips.created_at', 'pickup_locations.location as pickup_location', 'pickup_locations.location_name as pickup_location_name', 'drop_locations.location as drop_location', 'trips.status', 'trips.id')
			->leftJoin('drivers', 'trips.driver_id', '=', 'drivers.id')
			->leftJoin('customers', 'trips.client_id', '=', 'customers.id')
			->leftJoin('pickup_locations', 'trips.pickup_location_id', '=', 'pickup_locations.id')
			->leftJoin('drop_locations', 'trips.drop_location_id', '=', 'drop_locations.id')
			->whereIn('driver_id', $driver_id)
			->whereIn('status', $statusCode) //fetch all the trips except new trips
			->whereNull('trips.deleted_at') //exclude deleted at records
			->where(function ($query) use ($trip_type) {
				if ($trip_type != 'all') {
					$query->where('trips.status', $trip_type);
				}
			})
			->where(function ($query) use ($driver) {
				if ($driver != '') {
					$query->where('drivers.first_name', 'like', '%' . $driver . '%');
					$query->orWhere('drivers.last_name', 'like', '%' . $driver . '%');
				}
			})
			->where(function ($query) use ($location) {
				if ($location != '') {
					$query->where('pickup_locations.location', 'like', '%' . $location . '%');
					$query->orWhere('pickup_locations.location_name', 'like', '%' . $location . '%');
					$query->orWhere('drop_locations.location', 'like', '%' . $location . '%');
				}
			})
			->where(function ($query) use ($created_at) {
				if ($created_at != '') {
					$query->whereDate('trips.created_at', '=', $created_at);
				}
			})
			->where(function ($query) use ($from_date) {
				if ($from_date != '') {
					$query->where('trips.created_at', '>=', $from_date);
				}
			})
			->where(function ($query) use ($to_date) {
				if ($to_date != '') {
					$query->where('trips.created_at', '<=', $to_date);
				}
			})
			->orderBy('trips.id', 'desc')
			->get();

		return $response;
	}

	public static function fetchAllDrivers($merchant_id, $vehicle, $company_user)
	{
		// return $merchant_id;

		if (session('user_role') == 2) {
			$user_id = Auth::id();
		} else {
			$user_id = '';
		}

		$response = DB::table('drivers')
			->where(function ($query) use ($merchant_id) {
				if ($merchant_id != ' ') {
					$query->where('merchant_id', $merchant_id);
				}
			})
			->where(function ($query) use ($vehicle) {
				if ($vehicle != '') {
					$query->where('vehicle_id', 'like', '%' . $vehicle . '%');
				}
			})
			->orderBy('id', 'desc')
			->get();

		return $response;
	}


	public static function fetchAllUsers($name, $email, $phone, $user_type, $role = null)
	{

		$response = DB::table('users')->where('user_type', 3)
			->where(function ($query) use ($role) {
				if ($role != 'all') {
					if ($role == 1) {
						$query->where('is_payment_user', 0); // Normal User
					}
					if ($role == 2) {
						$query->where('is_payment_user', 1); // Payment User
					}
				}
			})
			->where(function ($query) use ($user_type) {
				if ($user_type != 'all') {
					$query->where('is_active', $user_type);
				}
			})
			->where(function ($query) use ($name) {
				if ($name != '') {
					$query->where('first_name', 'like', '%' . $name . '%');
					$query->orWhere('last_name', 'like', '%' . $name . '%');
				}
			})
			->where(function ($query) use ($email) {
				if ($email != '') {
					$query->where('email', 'like', '%' . $email . '%');
				}
			})
			->where(function ($query) use ($phone) {
				if ($phone != '') {
					$query->where('phone', 'like', '%' . $phone . '%');
				}
			})
			->orderBy('id', 'desc')
			->get();

		return $response;
	}

	public static function AlluserByemail($email, $user_type)
	{

		$response = DB::table('users')->where('email', $email)->where('user_type', $user_type)->get();

		return $response;
	}

	public static function fetchAllMerchants($name, $email, $phone, $user_type)
	{

		$response = DB::table('users')->where('user_type', '=', 4)
			->where(function ($query) use ($user_type) {
				if ($user_type != 'all') {
					$query->where('is_active', $user_type);
				}
			})
			->where(function ($query) use ($name) {
				if ($name != '') {
					$query->where('merchant_name', 'like', '%' . $name . '%');
				}
			})
			->where(function ($query) use ($email) {
				if ($email != '') {
					$query->where('email', 'like', '%' . $email . '%');
				}
			})
			->where(function ($query) use ($phone) {
				if ($phone != '') {
					$query->where('phone', 'like', '%' . $phone . '%');
				}
			})
			->orderBy('id', 'desc')
			->get();

		return $response;
	}

	public static function getcategory()
	{
		$response = DB::table('categories')
			->select('categories.id', 'categories.url', 'categories.title', DB::raw('count(blogs.category_id) as category_count'))
			->leftjoin('blogs', 'categories.id', '=', 'blogs.category_id')
			->groupBy('categories.url', 'categories.title', 'categories.id')
			->where('categories.is_active', 1)
			->where('blogs.is_active', 1)
			->orderBy('id', 'asc')
			->get();
		return $response;
	}

	public static function getTripReport($truck_id, $daterange, $trip_type, $customer_phone = null, $payment_type = null)
	{
		$customer_id = null;

		if ($customer_phone) {
			$customer = Customer::where('phone_number', $customer_phone)->first();
			if ($customer) {
				$customer_id = $customer->id;
			}
		}

		if ($daterange == null || $daterange == '') {
			$created_at = '';
			$from_date = '';
			$to_date = '';
		} else {
			$date = explode('to', $daterange);
			if (count($date) == 1) {
				$created_at = Carbon::parse($date[0])->toDateTimeString();
				$from_date = '';
				$to_date = '';
			} else {
				$created_at = '';
				$from_date = Carbon::parse($date[0])->toDateTimeString();
				$to_date = Carbon::parse($date[1])->toDateTimeString();
			}
		}
		if ($truck_id !== null) {
			$driver = Driver::where('vehicle_id', $truck_id)->select('id', 'vehicle_id')->first();

			if (session('user_role') == 1) {
				$driver = Driver::where('vehicle_id', $truck_id)->select('id', 'vehicle_id')->first();
			} else {
				$driver = Driver::where(['vehicle_id' => $truck_id, 'user_id' => Auth::user()->id])->select('id', 'vehicle_id')->first();
			}
			if ($driver) {
				$driver_id = [$driver->id];
			} else {
				$driver_id = [$truck_id];
			}
		} else {
			if (session('user_role') == 1) {
				$driver_id = Driver::pluck('id');
			} else {
				$driver_id = DB::table('drivers')->where('user_id', Auth::user()->id)->pluck('id');
			}
		}

		$trips_detail = DB::table('trips')
			->select('drivers.first_name', 'drivers.last_name', 'trips.driver_id', 'trips.client_id', 'trips.total_cost', 'trips.cost_of_sand', 'trips.road_money', 'trips.driver_commission', 'trips.client_name', 'trips.trip_generated_at', 'trips.created_at', 'pickup_locations.location as pickup_location', 'pickup_locations.location_name', 'drop_locations.location as drop_location', 'trip_payments.transaction_id', 'trip_payments.status as trip_payment_status', 'trips.status', 'trips.id', 'trips.payment_confirm_by_bank_transfer', 'trips.payment_confirmed_by_pos')
			->leftJoin('drivers', 'trips.driver_id', '=', 'drivers.id')
			->leftJoin('pickup_locations', 'trips.pickup_location_id', '=', 'pickup_locations.id')
			->leftJoin('drop_locations', 'trips.drop_location_id', '=', 'drop_locations.id')
			->leftJoin('trip_payments', 'trips.id', '=', 'trip_payments.trip_id')

			->whereIn('trips.driver_id', $driver_id)
			->where(function ($query) use ($trip_type, $driver_id) {
				if ($trip_type == 'all') {
					$query->whereIn('trips.driver_id', $driver_id);
				} else {
					$query->where('trips.status', $trip_type);
				}
			})
			->where(function ($query) use ($customer_id) {
				if (!empty($customer_id)) {
					$query->where('trips.client_id', '=', $customer_id);
				}
			})
			->where(function ($query) use ($created_at) {
				if ($created_at != '') {
					$query->whereDate('trips.created_at', '=', $created_at);
				}
			})
			->where(function ($query) use ($from_date) {
				if ($from_date != '') {
					$query->whereDate('trips.created_at', '>=', $from_date);
				}
			})
			->where(function ($query) use ($to_date) {
				if ($to_date != '') {
					$query->whereDate('trips.created_at', '<=', $to_date);
				}
			})
			->orderBy('trips.id', 'desc')
			->get()->map(function ($item) {
				// Map payment status 
				if ($item->payment_confirm_by_bank_transfer) {
					$item->payment_status = 'Bank Transfer';
				} elseif ($item->payment_confirmed_by_pos) {
					$item->payment_status = 'POS Payment';
				} else {
					if (($item->status == 1 || $item->status == 2) && !$item->transaction_id) {
						$item->payment_status =  '--';
					} else if ($item->status == 3 && !$item->transaction_id) {
						$item->payment_status = 'Pending';
					} else if ($item->transaction_id) {
						$item->payment_status = 'Paid';
					} else if ($item->transaction_id && $item->trip_payment_status !== 'successful') {
						$item->payment_status = 'Failed';
					} else {
						$item->payment_status = '--';
					}
				}

				if (!$item->trip_generated_at) {
					$item->trip_generated_at = $item->created_at;
				}
				$item->total_trip_cost = $item->total_cost + ($item->cost_of_sand ?? 0) + ($item->road_money ?? 0);
				return $item;
			});

		$trips_detail = collect($trips_detail)->filter(function ($item) use ($payment_type) {
			if ($payment_type) {
				return $payment_type == $item->payment_status;
			}
			return true;
		});

		if (count($trips_detail) > 0) {
			$total_earning = 0;
			$total_commission = 0;
			$completed_trips = 0;
			$cancel_trips = 0;
			$live_trips = 0;
			$paid_trips = 0;
			$trip_due_amount = 0;
			$new_trips = 0;
			foreach ($trips_detail as $item) {
				if ($item->status == 1) {
					$new_trips += 1;
				}
				if ($item->status == 2) {
					$live_trips += 1;
				}

				if ($item->payment_status == 'Paid') {
					$paid_trips += 1;
				} else {
					if ($item->status == 3 && !$item->payment_confirm_by_bank_transfer && !$item->payment_confirmed_by_pos) {
						$trip_due_amount += $item->total_trip_cost;
					}
				}

				if ($item->status == 3) {
					$completed_trips += 1;

					$total_earning += $item->total_cost;
					$total_commission += $item->driver_commission;
					$profit = $total_earning - $total_commission;
				} else {
					$total_earning += 0;
					$total_commission += 0;
					$profit = 0;
				}
				if ($item->status == 4) {
					$cancel_trips += 1;
				}
			}

			$report = [
				"total_earning" => intval($total_earning),
				"total_commission" => intval($total_commission),
				"profit" => intval($profit),
				"completed_trips" => intval($completed_trips),
				"cancel_trips" => intval($cancel_trips),
				"live_trips" => intval($live_trips),
				"paid_trips" => intval($paid_trips),
				"trip_due_amount" => intval($trip_due_amount),
				"new_trips" => intval($new_trips),
				"trips_detail" => $trips_detail->values()
			];

			return ["status" => "ok", "message" => "success", "data" => $report];
		} else {
			return ["status" => "ok", "message" => "Report not found", "data" => null];
		}
	}


	public static function getExpenseData($daterange, $item_name = "", $driver_phone = "", $driver_id = null)
	{

		$user = Auth::user();
		$condition = ['drivers.user_id' => $user->id];

		$driver_ids = Driver::where('phone', 'like', '%' . $driver_phone . '%')->pluck('id')->toArray();

		if ($daterange == null || $daterange == '') {
			$created_at = '';
			$from_date = '';
			$to_date = '';
		} else {
			$date = explode('to', $daterange);
			if (count($date) == 1) {
				$created_at = Carbon::parse($date[0])->toDateTimeString();
				$from_date = '';
				$to_date = '';
			} else {
				$created_at = '';
				$from_date = Carbon::parse($date[0])->toDateTimeString();
				$to_date = Carbon::parse($date[1])->toDateTimeString();
			}
		}

		$data = DB::table('expenses')
			->select('expenses.id', 'expenses.item_name', 'expenses.item_quantity', 'expenses.item_cost', 'expenses.driver_id', 'expenses.created_at', 'drivers.first_name', 'drivers.last_name',  'drivers.phone')
			->leftjoin('drivers', 'drivers.id', '=', 'expenses.driver_id')
			->where($condition)
			->whereIn('expenses.driver_id', $driver_ids)
			->where(function ($query) use ($created_at) {
				if ($created_at != '') {
					$query->whereDate('expenses.created_at', '=', $created_at);
				}
			})
			->where(function ($query) use ($driver_id) {
				if ($driver_id) {
					$query->where('expenses.driver_id', $driver_id);
				}
			})
			->where(function ($query) use ($item_name) {
				if ($item_name != '') {
					$query->where('expenses.item_name', 'like', '%' . $item_name . '%');
				}
			})
			->where(function ($query) use ($from_date) {
				if ($from_date != '') {
					$query->whereDate('expenses.created_at', '>=', $from_date);
				}
			})
			->where(function ($query) use ($to_date) {
				if ($to_date != '') {
					$query->whereDate('expenses.created_at', '<=', $to_date);
				}
			})
			->get();
		return $data;
	}
}
