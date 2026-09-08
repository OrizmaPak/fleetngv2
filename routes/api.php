<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\DriverController;
use App\Http\Controllers\Api\ExpenseController;

/* Driver Api route starts here */

if (app()->environment('local')) {
    Route::get('test-notification1/{id}', [DriverController::class, 'testFunction']);
    Route::get('test-notification2/{id}', [DriverController::class, 'testFunction2']);
    Route::get('test-notification3/{id}', [DriverController::class, 'testFunction3']);
    Route::get('test-trip-notification', [DriverController::class, 'testTripFunction']);
}
Route::post('driver-login', [DriverController::class, 'driver_login']);
Route::post('driver-logout', [DriverController::class, 'driver_logout']);
Route::post('driver-total-commission', [DriverController::class, 'driver_total_commission']);
Route::post('add-new-trip', [DriverController::class, 'add_new_trip']);
Route::post('start-clock', [DriverController::class, 'start_clock']);
Route::post('stop-clock', [DriverController::class, 'stop_clock']);
Route::get('show-drop-location', [DriverController::class, 'show_drop_location']);
Route::get('show-pickup-location', [DriverController::class, 'show_pickup_location']);
Route::post('driver-completed-trip', [DriverController::class, 'driver_completed_trip']);
Route::post('driver-trip-commission', [DriverController::class, 'driver_trip_commission']);
Route::post('driver-running-trip', [DriverController::class, 'driver_running_trip']);
Route::post('driver-last-active', [DriverController::class, 'driver_last_active']);
Route::post('all-driver-complete-trip', [DriverController::class, 'all_completed_trip']);
Route::post('all-driver-commission', [DriverController::class, 'all_commission_trip']);
Route::post('running-trip', [DriverController::class, 'running_trip']);
Route::post('trip', [DriverController::class, 'trip']);
Route::post('update-trip', [DriverController::class, 'update_trip']);
Route::post('pic-location', [DriverController::class, 'pic_location']);

Route::get('pending-trips', [DriverController::class, 'pendingTrip']);
Route::get('single-trip', [DriverController::class, 'singleTrip']);
Route::get('driver-stats', [DriverController::class, 'driverStats']);
Route::get('driver-notifications', [DriverController::class, 'tripRequests']);
Route::post('driver-notifications/read_all', [DriverController::class, 'tripRequestsReadAll']);
Route::post('driver-notifications/{id}/delete', [DriverController::class, 'tripRequestsDelete']);
Route::post('driver-trip/{id}/accept', [DriverController::class, 'driverTripaccept']);
Route::post('driver-trip/{id}/decline', [DriverController::class, 'driverTripDecline']);
Route::post('driver-device-token', [DriverController::class, 'driverDeviceToken']);


Route::get('expenses', [ExpenseController::class, 'expenses']);
Route::get('expenses/{id}', [ExpenseController::class, 'single_expense']);
Route::post('add-expense', [ExpenseController::class, 'add_expense']);
/* Driver Api route ends here */

/* Admin Api route starts here */

Route::post('admin-login', [ApiController::class, 'admin_login']);
Route::post('forgot-password', [ApiController::class, 'forgot_password']);
Route::post('change-password', [ApiController::class, 'change_password']);

Route::post('add-driver', [ApiController::class, 'add_driver']);
Route::post('change-driver-pin', [ApiController::class, 'change_driver_pin']);
Route::post('get-driver-detail', [ApiController::class, 'get_driver_detail']);
Route::post('update-driver-detail', [ApiController::class, 'update_driver_detail']);
Route::post('change-driver-status', [ApiController::class, 'change_driver_status']);
Route::post('delete-driver', [ApiController::class, 'delete_driver']);

Route::post('get-trip-detail', [ApiController::class, 'get_trip_detail']);
Route::post('cancel-trip', [ApiController::class, 'cancel_trip']);
Route::post('delete-trip', [ApiController::class, 'delete_trip']);
Route::post('change-trip-driver', [ApiController::class, 'change_trip_driver']);
Route::post('add-pickup-location', [ApiController::class, 'add_pickup_location']);
Route::post('get-pickup-location-detail', [ApiController::class, 'get_pickup_location_detail']);
Route::post('update-pickup-location-detail', [ApiController::class, 'update_pickup_location_detail']);
Route::post('delete-pickup-location', [ApiController::class, 'delete_pickup_location']);
Route::post('change-pickup-location-status', [ApiController::class, 'change_pickup_location_status']);


//user api

Route::post('get-user-detail', [ApiController::class, 'get_user_detail']);
Route::post('update-user-detail', [ApiController::class, 'update_user_detail']);
Route::post('add-user', [ApiController::class, 'add_user']);
Route::post('change-user-status', [ApiController::class, 'change_user_status']);
Route::post('delete-user', [ApiController::class, 'delete_user']);


/* Admin Api route ends here */

/* Super Admin Api route starts here */

Route::post('superadmin-login', [ApiController::class, 'superadmin_login']);

//merchant api

Route::post('get-merchant-detail', [ApiController::class, 'get_merchant_detail']);
Route::post('update-merchant-detail', [ApiController::class, 'update_merchant_detail']);
Route::post('add-merchant', [ApiController::class, 'add_merchant']);
Route::post('change-merchant-status', [ApiController::class, 'change_merchant_status']);
Route::post('delete-merchant', [ApiController::class, 'delete_merchant']);

/* Super Admin Api route ends here */

if (app()->environment('production')) {
    URL::forceScheme('https');
}
