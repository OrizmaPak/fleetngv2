<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\MiscellaneousController;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\DriverController;
use App\Http\Controllers\Admin\TripController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\TrackingController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\PaymentsController;
use App\Http\Controllers\PolicyPageController;
use App\Http\Controllers\SMSController;
use App\Http\Controllers\UiSearchController;
use App\Http\Controllers\Superadmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\Superadmin\UserController as SuperAdminUserController;
use App\Http\Controllers\Superadmin\MerchantController as SuperAdminMerchantController;
use App\Http\Controllers\Superadmin\PaymentUserController;
use App\Http\Controllers\Superadmin\SettingsController as SuperAdminSettingsController;


/* Admin panel pages routes starts here */

Route::view('/', 'front.index');
Route::get('refund-policy', [FrontController::class, 'refund_policy']);
Route::get('privacy-policy', [FrontController::class, 'privacy_policy']);
Route::get('privacy-policy', [FrontController::class, 'privacy_policy']);
Route::get('terms-conditions', [FrontController::class, 'terms_conditions']);
Route::get('contact-us', [FrontController::class, 'contact_us']);
Route::post('submit-contact-us', [FrontController::class, 'submit_contact_request'])->name('submit-contact-us');

Route::match(['get', 'post'], 'login', [AuthenticationController::class, 'admin_login'])->name('auth-login');
Route::match(['get', 'post'], '/admin', [AuthenticationController::class, 'admin_login'])->name('auth-admin-login');
Route::match(['get', 'post'], '/user', [AuthenticationController::class, 'user_login'])->name('auth-user-login');
// Route::match(['get', 'post'], '/user-login', [AuthenticationController::class, 'payment_user_login'])->name('auth-user-login');
Route::get('logout', [AuthenticationController::class, 'logout'])->name('logout');
Route::match(['get', 'post'], 'forgot-password', [AuthenticationController::class, 'forgot_password'])->name('auth-forgot-password');
Route::match(['get', 'post'], 'reset-password/{token}', [AuthenticationController::class, 'reset_password'])->name('auth-reset-password');
Route::get('reports-api', [ReportController::class, 'report_api']);
Route::get('ui/search', UiSearchController::class)->middleware('auth')->name('ui-search');

Route::group(['namespace' => 'Admin', 'middleware' => 'auth'], function () {

  Route::get('analytics', [DashboardController::class, 'dashboardAnalytics'])->name('dashboard-analytics');

  /* Route User Management Start */
  Route::middleware(['auth.user'])->group(function () {
    Route::get('user-list', [UserController::class, 'user_list'])->name('admin-user-list');
    Route::get('user-list-detail', [UserController::class, 'user_list_detail'])->name('user-list-detail');
    Route::get('user-list-detail-filter', [UserController::class, 'user_list_detail_filter'])->name('user-list-detail-filter');
    Route::match(['get', 'post'], 'user/add', [UserController::class, 'add_user'])->name('add-user');
    Route::match(['get', 'post'], 'user/edit/{id}', [UserController::class, 'edit_user'])->name('edit-user');
    Route::match(['get', 'post'], 'user/view/{id}', [UserController::class, 'view_user'])->name('view-user');
    Route::post('user/status', [UserController::class, 'user_status'])->name('user-status');
    Route::post('user/delete', [UserController::class, 'user_delete'])->name('user-delete');
  });

  Route::get('user-trip-list/{id}', [UserController::class, 'user_trip_list'])->name('user-trip-list');
  Route::get('user-trip-list-detail/{id}', [UserController::class, 'user_trip_list_detail'])->name('user-trip-list-detail');

  /* Route User Management End */

  /* Route Driver Management Start */
  Route::get('driver-list', [DriverController::class, 'driver_list'])->name('driver-list');
  Route::get('driver-list-detail', [DriverController::class, 'driver_list_detail'])->name('driver-list-detail');
  Route::get('driver-list-detail-filter', [DriverController::class, 'driver_list_detail_filter'])->name('driver-list-detail-filter');
  Route::post('driver/change-login-pin', [DriverController::class, 'change_login_pin'])->name('change-login-pin');
  Route::match(['get', 'post'], 'driver/add', [DriverController::class, 'add_driver'])->name('add-driver');
  Route::match(['get', 'post'], 'driver/edit/{id}', [DriverController::class, 'edit_driver'])->name('edit-driver');
  Route::match(['get', 'post'], 'driver/view/{id}', [DriverController::class, 'view_driver'])->name('view-driver');
  Route::post('driver/status', [DriverController::class, 'driver_status'])->name('driver-status');
  Route::post('driver/delete', [DriverController::class, 'driver_delete'])->name('driver-delete');
  Route::get('driver-trip-list/{id}', [DriverController::class, 'driver_trip_list'])->name('driver-trip-list');
  Route::get('driver-trip-list-detail/{id}', [DriverController::class, 'driver_trip_list_detail'])->name('driver-trip-list-detail');
  /* Route Driver Management End */

  /* Route Expense Management Start */
  Route::get('expenses', [ExpenseController::class, 'expenses'])->name('expenses');
  Route::get('expenses-list', [ExpenseController::class, 'expenses_list'])->name('expenses-list');
  Route::post('expense-delete', [ExpenseController::class, 'delete_expense'])->name('expense-delete');
  Route::post('expense-edit', [ExpenseController::class, 'edit_expense'])->name('expense-edit');
  /* Route Expense Management Ens */

  /* Route Payment Management Start */
  Route::get('payment-list', [PaymentsController::class, 'payment_list'])->name('payment-list');
  /* Route Payment Management End */

  /* Route Client Management Start */
  Route::get('client-list', [ClientController::class, 'client_list'])->name('client-list');
  Route::get('client-list-detail', [ClientController::class, 'client_list_detail'])->name('client-list-detail');
  Route::match(['get', 'post'], 'client-edit/{id}', [ClientController::class, 'client_edit'])->name('client-edit');
  Route::get('client-status-change/{id}', [ClientController::class, 'client_status_change'])->name('client-status-change');
  Route::post('client/delete', [ClientController::class, 'client_delete'])->name('client-delete');
  /* Route Client Management End */

  /* Route Trip Management Start */
  Route::get('trip-list', [TripController::class, 'trip_list'])->name('trip-list');
  Route::post('trip/{id}/pos-payment-confirm', [TripController::class, 'trip_confirm_pos'])->name('trip_confirm_pos');
  Route::post('trip/{id}/bank-payment-received', [TripController::class, 'trip_bank_payment_received'])->name('trip_bank_payment_received');
  Route::post('trip/bulk-bank-payment-received', [TripController::class, 'bulk_trip_bank_payment_received'])->name('bulk_trip_bank_payment_received');
  Route::post('trip/bulk-pos-payment-received', [TripController::class, 'bulk_trip_pos_payment_received'])->name('bulk_trip_pos_payment_received');
  Route::get('trip-list-detail', [TripController::class, 'trip_list_detail'])->name('trip-list-detail');
  Route::get('trip-list-detail-filter', [TripController::class, 'trip_list_detail_filter'])->name('trip-list-detail-filter');
  Route::match(['get', 'post'], 'trip/add', [TripController::class, 'trip_add'])->name('trip-add');
  Route::post('trip/cancel', [TripController::class, 'trip_cancel'])->name('trip-cancel');
  Route::post('trip/delete', [TripController::class, 'trip_delete'])->name('trip-delete');
  Route::post('trip/send-sms-payment-alert-to-client', [SMSController::class, 'send_sms_payment_alert_to_client'])->name('send-sms-payment-alert-to-client');
  Route::post('trip/change-driver', [TripController::class, 'change_trip_driver'])->name('update-trip-driver');
  Route::get('trip/detail/{id}', [TripController::class, 'trip_detail'])->name('trip-detail');
  Route::get('trip-live-location/{id}', [TripController::class, 'trip_live_location'])->name('trip-live-location');

  /* Route Trip Management Start */

  /* Route Pickup Location Management Start */
  Route::get('location-list', [LocationController::class, 'location_list'])->name('location-list');
  Route::get('pickup-location-list-detail', [LocationController::class, 'pickup_location_list_detail'])->name('pickup_location-list-detail');
  Route::match(['get', 'post'], 'pickup-location/add', [LocationController::class, 'add_pickup_location'])->name('add-pickup-location');
  Route::match(['get', 'post'], 'pickup-location/edit/{id}', [LocationController::class, 'edit_pickup_location'])->name('edit-pickup-location');
  Route::match(['get', 'post'], 'pickup-location/view/{id}', [LocationController::class, 'view_pickup_location'])->name('view-pickup-location');
  Route::post('pickup-location/delete', [LocationController::class, 'pickup_location_delete'])->name('pickup-location-delete');
  Route::post('pickup-location/status', [LocationController::class, 'pickup_location_status'])->name('pickup-location-status');

  /* Route Pickup Location Management End */

  /* Route Live Tracking Management Start */
  Route::get('live-tracking', [TrackingController::class, 'live_tracking'])->name('live-tracking');
  Route::match(['get', 'post'], 'map-tracking/{id}', [TrackingController::class, 'map_tracking'])->name('map-tracking');
  Route::match(['get', 'post'], 'map-history', [TrackingController::class, 'map_history'])->name('map-history');
  Route::get('tracking-list-detail', [TrackingController::class, 'tracking_list_detail'])->name('tracking-list-detail');
  Route::get('tracking-list-detail-filter', [TrackingController::class, 'tracking_list_detail_filter'])->name('tracking-list-detail-filter');

  /* Route Live Tracking Management End */

  /* Route Report Management Start */

  Route::get('reports', [ReportController::class, 'report_index'])->name('report-detail');
  Route::get('report-pdf-download', [ReportController::class, 'report_pdf_download'])->name('report_pdf_download');


  /* Route Report Management Start */
  Route::get('account-settings', [SettingsController::class, 'account_settings'])->name('page-account-settings');
  Route::post('change-password', [SettingsController::class, 'change_password']);
});
// Download trip invoice 
Route::get('trip/{id}/invoice/pdf-download', [PaymentsController::class, 'trip_invoice_pdf_download'])->name('report_invoice_pdf_download'); // Trip invoice

// GET Vehicle number
Route::match(['get', 'post'], '/Vehicle/no', [DriverController::class, 'vehicle_no'])->name('vehicle-no')->middleware('auth');

/* Super Admin panel pages routes starts here */

Route::match(['get', 'post'], '/superadmin', [AuthenticationController::class, 'superadmin_login'])->name('auth-superadmin-login');

// SuperAdmin Routes
Route::group(['prefix' => 'superadmin', 'namespace' => 'Superadmin', 'middleware' => 'auth'], function () {

  Route::get('analytics', [SuperAdminDashboardController::class, 'dashboardAnalytics'])->name('super-dashboard-analytics');
  Route::match(['get', 'post'], 'pages/refund-policy', [PolicyPageController::class, 'refund_policy']);
  Route::match(['get', 'post'], 'pages/privacy-policy', [PolicyPageController::class, 'privacy_policy']);
  Route::match(['get', 'post'], 'pages/terms-conditions', [PolicyPageController::class, 'terms_conditions']);

  /* Route User Management Start */
  Route::get('user-list', [SuperAdminUserController::class, 'user_list'])->name('user-list');
  Route::get('user-list-detail', [SuperAdminUserController::class, 'user_list_detail'])->name('superadmin-user-list-detail');
  Route::get('user-list-detail-filter', [SuperAdminUserController::class, 'user_list_detail_filter'])->name('superadmin-user-list-detail-filter');
  Route::match(['get', 'post'], 'user/add', [SuperAdminUserController::class, 'add_user'])->name('add-user-page');
  Route::match(['get', 'post'], 'user/edit/{id}', [SuperAdminUserController::class, 'edit_user'])->name('superadmin-edit-user');
  Route::match(['get', 'post'], 'user/view/{id}', [SuperAdminUserController::class, 'view_user'])->name('superadmin-view-user');
  Route::post('user/status', [SuperAdminUserController::class, 'user_status'])->name('superadmin-user-status');
  Route::post('user/delete', [SuperAdminUserController::class, 'user_delete'])->name('superadmin-user-delete');
  Route::get('merchant/list', [SuperAdminMerchantController::class, 'merchant_list_data'])->name('merchant');
  /* Route User Management End */

  /* Route Merchant Management Start */
  Route::get('merchant-list', [SuperAdminMerchantController::class, 'merchant_list'])->name('merchant-list');
  Route::get('merchant-list-detail', [SuperAdminMerchantController::class, 'merchant_list_detail'])->name('merchant-list-detail');
  Route::get('merchant-list-detail-filter', [SuperAdminMerchantController::class, 'merchant_list_detail_filter'])->name('merchant-list-detail-filter');
  Route::match(['get', 'post'], 'merchant/add', [SuperAdminMerchantController::class, 'add_merchant'])->name('add-merchant');
  Route::match(['get', 'post'], 'merchant/edit/{id}', [SuperAdminMerchantController::class, 'edit_merchant'])->name('edit-merchant');
  Route::match(['get', 'post'], 'merchant/view/{id}', [SuperAdminMerchantController::class, 'view_merchant'])->name('view-merchant');
  Route::post('merchant/status', [SuperAdminMerchantController::class, 'merchant_status'])->name('merchant-status');
  Route::post('merchant/delete', [SuperAdminMerchantController::class, 'merchant_delete'])->name('merchant-delete');
  Route::post('merchant/data', [SuperAdminMerchantController::class, 'merchant_data'])->name('merchant-data');
  /* Route Merchant Management End */

  Route::get('account-settings', [SuperAdminSettingsController::class, 'account_settings'])->name('superadmin-page-account-settings');

  Route::get('user-list-byMerchant', [SuperAdminUserController::class, 'user_data'])->name('user-list-data');
});

Route::get('/error', [MiscellaneousController::class, 'error'])->name('error');

if (app()->environment('production')) {
  URL::forceScheme('https');
}
