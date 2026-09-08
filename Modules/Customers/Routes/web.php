<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Modules\Customers\Http\Controllers\PaymentController;

Route::get('customer/trips/payments', [PaymentController::class, 'tripPayment']); // update payment detail in database for the trip



