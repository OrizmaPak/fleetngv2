<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('drivers');
            $table->foreignId('pickup_location_id')->nullable()->constrained('pickup_locations');
            $table->foreignId('drop_location_id')->nullable()->constrained('drop_locations');
            $table->integer('total_cost')->nullable();
            $table->integer('cost_of_sand')->nullable();
            $table->integer('road_money')->nullable();
            $table->string('client_name')->nullable();
            $table->dateTime('trip_generated_at')->nullable();
            $table->dateTime('pick_up_datetime')->nullable();
            $table->dateTime('clock_in_time')->nullable();
            $table->dateTime('clock_out_time')->nullable();
            $table->integer('driver_commission')->nullable();
            $table->string('piclocation')->nullable();
            $table->unsignedBigInteger('payment_id')->nullable()->index();
            $table->boolean('payment_confirm_by_bank_transfer')->default(false);
            $table->boolean('payment_confirmed_by_pos')->default(false);
            $table->unsignedInteger('total_time')->nullable();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->unsignedTinyInteger('status')->default(1)->comment('1: new, 2: live, 3: completed, 4: canceled, 5: declined');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('trips');
    }
}
