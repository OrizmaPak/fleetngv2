<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Customer;
use App\Models\PickupLocation;
use App\Models\DropLocation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PrepareReview extends Command
{
    protected $signature = 'fleetng:prepare-review';
    protected $description = 'Prepare isolated local review data without changing the application database.';

    public function handle()
    {
        if (!app()->environment(['local','testing'])) { $this->error('Review fixtures are local-only.'); return 1; }
        $directory = storage_path('app/review');
        if (!is_dir($directory)) mkdir($directory,0700,true);
        $database = $directory.'/review.sqlite';
        if (!is_file($database)) touch($database);
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>$database,'mail.default'=>'log']);
        DB::purge('sqlite');
        $this->call('migrate',['--force'=>true]);
        $accounts = [];
        foreach (['superadmin'=>1,'admin'=>2,'company'=>3,'payment'=>3,'merchant'=>4] as $name=>$role) {
            $accounts[$name] = User::firstOrCreate(['email'=>$name.'@review.fleetng.test'],['first_name'=>ucfirst($name),'last_name'=>'Review','merchant_name'=>$name === 'merchant' ? 'Review Haulage' : null,'user_type'=>$role,'is_active'=>1,'password'=>Hash::make('FleetNG-Review-2026!'),'is_payment_user'=>$name === 'payment' ? 1 : 0]);
        }
        foreach (['company','payment'] as $name) $accounts[$name]->update(['merchant_assigned'=>$accounts['merchant']->id]);
        $driver = Driver::firstOrCreate(['vehicle_id'=>'REVIEW-001'],['first_name'=>'Alex','last_name'=>'Driver','phone'=>'8000000010','user_id'=>$accounts['company']->id,'merchant_id'=>$accounts['merchant']->id,'device_serial_number'=>'REVIEW-DEVICE-001','login_pin'=>'1234','is_active'=>1]);
        $customer = Customer::firstOrCreate(['email'=>'customer@review.fleetng.test'],['first_name'=>'Taylor','last_name'=>'Customer','phone_number'=>'8000000000','country_code'=>'+234','is_active'=>1]);
        $pickup = PickupLocation::firstOrCreate(['location_name'=>'Review Depot'],['location'=>'Ikeja review depot','user_id'=>$accounts['company']->id,'latitude'=>6.6,'longitude'=>3.35,'is_active'=>1]);
        $drop = DropLocation::firstOrCreate(['location'=>'Lekki review site']);
        foreach ([1,2,3,4,5] as $status) {
            Trip::firstOrCreate(['client_id'=>$customer->id,'driver_id'=>$driver->id,'status'=>$status],['client_name'=>$customer->first_name.' '.$customer->last_name,'pickup_location_id'=>$pickup->id,'drop_location_id'=>$drop->id,'total_cost'=>85000,'cost_of_sand'=>15000,'road_money'=>5000,'trip_generated_at'=>now(),'pick_up_datetime'=>now()->addDays($status),'clock_in_time'=>$status === 3 ? now()->subHours(2) : null,'clock_out_time'=>$status === 3 ? now() : null]);
        }
        \Modules\Customers\Entities\DraftTrip::firstOrCreate(['client_id'=>$customer->id],['driver_id'=>$driver->id,'pickup_location_id'=>$pickup->id,'drop_location_id'=>$drop->id,'total_cost'=>60000,'client_name'=>'Taylor Customer','pick_up_datetime'=>now()->addDays(2)]);
        \App\Models\Expense::firstOrCreate(['driver_id'=>$driver->id,'item_name'=>'Review fuel'],['item_quantity'=>1,'item_cost'=>10000]);
        foreach (['Refund Policy','Privacy Policy','Terms & Conditions'] as $name) \App\Models\Page::firstOrCreate(['name'=>$name],['title'=>$name,'contents'=>'<p>Local review content only.</p>']);
        $manifest = ['database'=>$database,'accounts'=>array_map(function($user){ return ['id'=>$user->id,'email'=>$user->email]; },$accounts),'driver_id'=>$driver->id,'customer_id'=>$customer->id,'pickup_id'=>$pickup->id];
        file_put_contents($directory.'/manifest.json',json_encode($manifest,JSON_PRETTY_PRINT));
        $this->info('Review database and manifest prepared: '.$directory);
        $this->line('Staff password: FleetNG-Review-2026!; customer phone: 8000000000; OTP: 123456. Local review only.');
        return 0;
    }
}
