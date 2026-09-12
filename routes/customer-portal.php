<?php

use Illuminate\Support\Facades\Route;
use Modules\Customers\Http\Controllers\Api\AuthController;
use Modules\Customers\Http\Controllers\Api\DevelopmentOtpController;
use Modules\Customers\Http\Controllers\CustomerController;
use Modules\Customers\Http\Controllers\DraftTripController;
use Modules\Customers\Http\Controllers\TripController;
use Modules\Customers\Http\Controllers\PaymentController;
use Modules\Customers\Http\Middleware\PortalSession;
use Modules\Customers\Http\Middleware\CustomerAccess;

// Development integration entry. Production remains closed until verification and release gates pass.
Route::middleware(PortalSession::class)->group(function () {
    Route::view('customer-portal', 'customer-portal.index');
    Route::prefix('customer-portal/api')->group(function () {
        Route::post('otp/request', [DevelopmentOtpController::class, 'requestCode'])->middleware('throttle:customer-otp-request');
        Route::post('otp/verify', [DevelopmentOtpController::class, 'verifyCode'])->middleware('throttle:customer-otp-verify');
        Route::post('verify-phone', [AuthController::class, 'verifyPhone']);
        Route::post('verify-details', [AuthController::class, 'verifyDetails']);
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'store']);
        Route::middleware(CustomerAccess::class)->group(function () {
            Route::get('profile', [CustomerController::class, 'profile']);
            Route::post('profile', [CustomerController::class, 'profileUpdate']);
            Route::get('profile-image', [CustomerController::class, 'profileImage']);
            Route::post('logout', [AuthController::class, 'logout']);
            Route::get('stats', [CustomerController::class, 'stats']);
            Route::get('pickup-locations', [TripController::class, 'pickupLocations']);
            Route::get('merchants', [CustomerController::class, 'merchants']);
            Route::get('trips', [TripController::class, 'allTrips']);
            Route::get('trips/{id}', [TripController::class, 'show'])->whereNumber('id');
            Route::patch('trips/{id}/driver', [TripController::class, 'tripDriver'])->whereNumber('id');
            Route::post('trips/payment-link', [PaymentController::class, 'tripPaymentLink']);
            Route::get('trip-payments', [PaymentController::class, 'tripPayments']);
            Route::get('checkouts/{reference}', [PaymentController::class, 'checkoutStatus']);
            Route::get('draft/trips', [DraftTripController::class, 'allTrips']);
            Route::post('draft/trips/create', [DraftTripController::class, 'store']);
            Route::get('draft/trips/{id}', [DraftTripController::class, 'show'])->whereNumber('id');
            Route::post('draft/trips/{id}', [DraftTripController::class, 'update'])->whereNumber('id');
            Route::post('draft/trips/{id}/confirm', [DraftTripController::class, 'tripConfirm'])->whereNumber('id');
        });
    });
});
