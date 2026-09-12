<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\TripPayment;
use App\Models\Customer;
use App\Models\PickupLocation;
use App\Models\DropLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfOutputTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorized_invoice_and_report_are_downloadable_pdfs()
    {
        $user = User::create(['first_name'=>'Review','email'=>'pdf@example.test','user_type'=>1,'is_active'=>1]);
        $driver = Driver::create(['first_name'=>'Alex','last_name'=>'Review','user_id'=>$user->id,'vehicle_id'=>'REVIEW-PDF']);
        $customer = Customer::create(['first_name'=>'Taylor','phone_number'=>'8000000098','email'=>'pdf-client@example.test']);
        $pickup = PickupLocation::create(['location'=>'Review depot','location_name'=>'Depot']);
        $drop = DropLocation::create(['location'=>'Review delivery site']);
        $trip = Trip::create(['driver_id'=>$driver->id,'client_id'=>$customer->id,'client_name'=>'Taylor Review','pickup_location_id'=>$pickup->id,'drop_location_id'=>$drop->id,'total_cost'=>100,'road_money'=>20,'cost_of_sand'=>30,'trip_generated_at'=>now(),'pick_up_datetime'=>now()]);
        $payment = TripPayment::create(['trip_id'=>$trip->id,'customer_id'=>$customer->id,'amount'=>150,'status'=>'successful','transaction_id'=>'REVIEW-TRANSACTION','reference_code'=>'REVIEW-REFERENCE','payment_type'=>'pos']);
        $trip->update(['payment_id'=>$payment->id]);
        $this->actingAs($user)->withSession(['user_role'=>1]);
        foreach (['invoice'=>'/trip/'.$trip->id.'/invoice/pdf-download','report'=>'/report-pdf-download'] as $name=>$url) {
            $response = $this->get($url)->assertOk()->assertHeader('Content-Type','application/pdf');
            $this->assertStringStartsWith('%PDF-', $response->getContent());
            if (getenv('FLEETNG_CAPTURE_PDFS')) {
                $directory = storage_path('app/review');
                if (!is_dir($directory)) mkdir($directory,0700,true);
                file_put_contents($directory.'/test-'.$name.'.pdf',$response->getContent());
            }
        }
    }
}
