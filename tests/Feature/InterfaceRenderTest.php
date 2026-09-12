<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\PickupLocation;
use App\Models\DropLocation;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InterfaceRenderTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_staff_interfaces_render_in_both_themes()
    {
        Mail::fake();
        $admin = User::create(['first_name'=>'Reviewer','email'=>'review@example.test','user_type'=>1,'is_active'=>1]);
        $merchant = User::create(['first_name'=>'Merchant','merchant_name'=>'Review Merchant','email'=>'merchant@example.test','user_type'=>4,'is_active'=>1]);
        $company = User::create(['first_name'=>'Company','email'=>'company@example.test','user_type'=>3,'merchant_assigned'=>$merchant->id,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Driver','last_name'=>'Review','user_id'=>$company->id,'merchant_id'=>$merchant->id,'vehicle_id'=>'REVIEW-001','phone'=>'8000000001','login_pin'=>'1234']);
        $client = Customer::create(['first_name'=>'Customer','email'=>'client@example.test','phone_number'=>'8000000002','is_active'=>1]);
        $pickup = PickupLocation::create(['user_id'=>$company->id,'location'=>'Review depot','location_name'=>'Depot','latitude'=>6.5,'longitude'=>3.3]);
        $drop = DropLocation::create(['location'=>'Review site']);
        $trip = Trip::create(['driver_id'=>$driver->id,'client_id'=>$client->id,'client_name'=>'Customer','pickup_location_id'=>$pickup->id,'drop_location_id'=>$drop->id,'total_cost'=>100,'trip_generated_at'=>now(),'pick_up_datetime'=>now()->addDay()]);
        $paths = ['/analytics','/user-list','/user/add','/user/edit/'.$company->id,'/user/view/'.$company->id,'/user-trip-list/'.$company->id,'/driver-list','/driver/add','/driver/edit/'.$driver->id,'/driver/view/'.$driver->id,'/driver-trip-list/'.$driver->id,'/client-list','/client-edit/'.$client->id,'/trip-list','/trip/add','/trip/detail/'.$trip->id,'/location-list','/pickup-location/add','/pickup-location/edit/'.$pickup->id,'/pickup-location/view/'.$pickup->id,'/expenses','/payment-list','/live-tracking','/map-history','/reports','/account-settings','/superadmin/analytics','/superadmin/user-list','/superadmin/user/add','/superadmin/user/edit/'.$company->id,'/superadmin/user/view/'.$company->id,'/superadmin/merchant-list','/superadmin/merchant/add','/superadmin/merchant/edit/'.$merchant->id,'/superadmin/merchant/view/'.$merchant->id,'/superadmin/pages/refund-policy','/superadmin/pages/privacy-policy','/superadmin/pages/terms-conditions','/superadmin/account-settings'];
        $failures = [];
        foreach (['legacy','fleetng-modern'] as $theme) {
            $pathsToViews = [resource_path('views')];
            if ($theme !== 'legacy') array_unshift($pathsToViews,resource_path('views/themes/'.$theme));
            app('view')->getFinder()->setPaths($pathsToViews);
            app('view')->getFinder()->flush();
            foreach ($paths as $path) {
                $response = $this->actingAs($admin)->withSession(['user_role'=>1])->get($path);
                if ($response->status() !== 200) {
                    $error = $response->exception ? $response->exception->getMessage() : 'HTTP '.$response->status();
                    $failures[] = $theme.' '.$path.': '.$error;
                }
            }
        }
        $this->assertSame([],$failures,implode("\n",$failures));
    }
}
