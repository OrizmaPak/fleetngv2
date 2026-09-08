<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Customers\Http\Controllers\Api\AuthController;
use Modules\Customers\Http\Controllers\CustomerController;
use Modules\Customers\Http\Controllers\DraftTripController;
use Modules\Customers\Http\Controllers\PaymentController;
use Modules\Customers\Http\Controllers\TripController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' => 'customer'], function () {
    // UnAuthorized routes
    Route::post('register', [AuthController::class, 'store']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('verify-phone', [AuthController::class, 'verifyPhone']); // check phone number exists in database
    Route::post('verify-details', [AuthController::class, 'verifyDetails']); // check customer details exists in database
    Route::get('pickup-locations', [TripController::class, 'pickupLocations']); // retunt all active pickup locations

    // Sanctum Auth API routes
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/stats', [CustomerController::class, 'stats']); // return customer profile data
        Route::get('/profile', [CustomerController::class, 'profile']); // return customer profile data
        Route::post('/profile', [CustomerController::class, 'profileUpdate']); // return customer profile data
        Route::post('/save-device-token', [CustomerController::class, 'saveDeviceToken']); // return customer profile data
        Route::get('/merchants', [CustomerController::class, 'merchants']); // return merchants list with driver

        // Trip routes
        Route::get('trips', [TripController::class, 'allTrips']); // get all trips data of customer
        Route::post('trips/create', [TripController::class, 'store']);  // create new trip
        Route::get('trips/{id}', [TripController::class, 'show']); // get single trip data
        Route::patch('trips/{id}/driver', [TripController::class, 'tripDriver']); // add driver for the trip
        Route::post('trips/payment-link', [PaymentController::class, 'tripPaymentLink']); // generate payment link
        Route::get('trip-payments', [PaymentController::class, 'tripPayments']); // get all payments of trips

        // Draft Trips routes
        Route::get('draft/trips', [DraftTripController::class, 'allTrips']); // get all draft trips data of customer
        Route::post('draft/trips/create', [DraftTripController::class, 'store']);  // create new draft trip
        Route::get('draft/trips/{id}', [DraftTripController::class, 'show']); // get single draft trip data
        Route::post('draft/trips/{id}', [DraftTripController::class, 'update']); // get single draft trip data
        Route::post('draft/trips/{id}/driver', [DraftTripController::class, 'tripDriver']); // add driver for the draft trip
        Route::post('draft/trips/{id}/confirm', [DraftTripController::class, 'tripConfirm']); // add driver for the draft trip
    });
});

Route::post('trips/{id}/send-notification', [TripController::class, 'tripSendNotification']);


// When user typed URL is not matched with none of the above then bellow code will run
Route::fallback(function () {
    return response()->badRequest('Invalid URL!');
});
