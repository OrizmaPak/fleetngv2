<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\FrontModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StaffAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_vehicle_filter_cannot_be_used_as_a_foreign_driver_id()
    {
        $user = User::create(['first_name'=>'Company','email'=>'report-own@example.test','user_type'=>3,'is_active'=>1]);
        $other = User::create(['first_name'=>'Other','email'=>'report-other@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Foreign','user_id'=>$other->id,'vehicle_id'=>'OTHER','is_active'=>1]);
        Trip::create(['driver_id'=>$driver->id,'client_name'=>'Private client','total_cost'=>100,'trip_generated_at'=>now()]);
        $this->actingAs($user);
        $report = \App\Models\ApiModel::getTripReport((string)$driver->id,null,null);
        $this->assertEmpty($report['data']);
    }

    public function test_unknown_and_foreign_tracking_devices_do_not_fall_back_to_first_vehicle()
    {
        config(['integrations.tracking_enabled'=>true,'integrations.tracking_username'=>'test','integrations.tracking_key'=>'test']);
        $user = User::create(['first_name'=>'Company','email'=>'tracking@example.test','user_type'=>3,'is_active'=>1]);
        Driver::create(['first_name'=>'Own','user_id'=>$user->id,'is_active'=>1,'device_serial_number'=>'own']);
        \Illuminate\Support\Facades\Http::fake(['app.zypsa.com/*'=>\Illuminate\Support\Facades\Http::response(['data'=>[
            'vehicles'=>[['device_id'=>10,'serial'=>'foreign'],['device_id'=>20,'serial'=>'own']],
            'vehicle_data'=>[],
        ]])]);
        $this->actingAs($user)->get('/map-tracking/10')->assertNotFound();
        $this->get('/map-tracking/999')->assertNotFound();
        \Illuminate\Support\Facades\Http::assertSentCount(4);
    }

    public function test_map_history_uses_separate_group_mode_query_parameter()
    {
        config(['integrations.tracking_enabled'=>true,'integrations.tracking_username'=>'test','integrations.tracking_key'=>'test']);
        $user = User::create(['first_name'=>'Company','email'=>'map-history@example.test','user_type'=>3,'is_active'=>1]);
        Driver::create(['first_name'=>'Own','user_id'=>$user->id,'is_active'=>1,'device_serial_number'=>'own']);
        \Illuminate\Support\Facades\Http::fake(function ($request) {
            $url = (string) $request->url();
            if (str_contains($url, 'tracking_api.php')) {
                $body = json_decode($request->body(), true);
                return \Illuminate\Support\Facades\Http::response(['data'=>[
                    'vehicles'=>[['device_id'=>20,'serial'=>'own','registration_no'=>'TRUCK1']],
                    'vehicle_data'=>$body['action'] === 'extra' ? [['serial'=>'own','arial_distance'=>0,'place'=>'Depot']] : [],
                ]]);
            }
            if (str_contains($url, 'report_vehicle_distance_result.php')) {
                return \Illuminate\Support\Facades\Http::response(['message'=>'No record found','data'=>['vehicledistance_data'=>[]]]);
            }
            return \Illuminate\Support\Facades\Http::response(['data'=>['trip_data'=>[]]]);
        });

        $this->actingAs($user)->post('/map-history', ['search'=>'TRUCK1'])->assertOk();

        \Illuminate\Support\Facades\Http::assertSent(function ($request) {
            $url = (string) $request->url();
            return str_contains($url, 'report_vehicle_distance_result.php')
                && str_contains($url, 'group_hour=24')
                && str_contains($url, 'group_mode=1')
                && !str_contains($url, 'group_hour=24group_mode');
        });
    }

    public function test_tracking_list_ajax_returns_valid_json_when_provider_fails()
    {
        config(['integrations.tracking_enabled'=>true,'integrations.tracking_username'=>'test','integrations.tracking_key'=>'test']);
        $user = User::create(['first_name'=>'Company','email'=>'tracking-fail@example.test','user_type'=>3,'is_active'=>1]);
        Driver::create(['first_name'=>'Own','user_id'=>$user->id,'is_active'=>1,'device_serial_number'=>'own']);
        \Illuminate\Support\Facades\Http::fake(['app.zypsa.com/*'=>\Illuminate\Support\Facades\Http::response('Provider down', 500)]);

        $this->actingAs($user)->get('/tracking-list-detail?draw=7', ['X-Requested-With'=>'XMLHttpRequest'])
            ->assertOk()
            ->assertJson([
                'draw' => 7,
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
            ]);
    }

    public function test_anonymous_admin_api_and_invoice_are_protected()
    {
        $this->postJson('/api/get-user-detail',['user_id'=>1])->assertUnauthorized();
        $this->postJson('/api/delete-driver',['driver_id'=>1])->assertUnauthorized();
        $this->getJson('/trip/1/invoice/pdf-download')->assertUnauthorized();
    }

    public function test_company_cannot_read_foreign_driver_or_enter_superadmin()
    {
        $user = User::create(['first_name'=>'Company','email'=>'company@example.test','user_type'=>3,'is_active'=>1]);
        $other = User::create(['first_name'=>'Other','email'=>'other@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Foreign','user_id'=>$other->id]);
        $trip = Trip::create(['driver_id'=>$driver->id,'total_cost'=>100]);
        $this->actingAs($user)->get('/driver/view/'.$driver->id)->assertNotFound();
        $this->get('/trip/'.$trip->id.'/invoice/pdf-download')->assertNotFound();
        $this->get('/superadmin/merchant-list')->assertForbidden();
        $this->post('/driver/delete',['driver_id'=>$driver->id])->assertNotFound();
        $this->assertDatabaseHas('drivers',['id'=>$driver->id]);
    }

    public function test_internal_api_uses_same_contract_without_network()
    {
        $user = User::create(['first_name'=>'Company','email'=>'company@example.test','user_type'=>3,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Own','user_id'=>$user->id]);
        $this->actingAs($user);
        config(['app.url'=>'http://unreachable.invalid/']);
        $response = FrontModel::callPostCurl('http://unreachable.invalid/api/get-driver-detail',['driver_id'=>$driver->id]);
        $this->assertTrue($response['success']);
        $this->assertEquals($driver->id,$response['data']['id']);
    }
}
