<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token')->nullable();
            $table->string('password')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('photo')->nullable();
            $table->integer('auth_pin')->nullable();
            $table->dateTime('last_active')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users');
            $table->string('device_serial_number', 64)->nullable();
            $table->string('voice_number', 32)->nullable();
            $table->bigInteger('billing_term')->nullable();
            $table->unsignedTinyInteger('user_type')->nullable();
            $table->unsignedBigInteger('merchant_id')->nullable()->index();
            $table->string('driver_id')->nullable();
            $table->text('device_token')->nullable();
            $table->boolean('is_active')->default(true)->comment('0: inactive, 1: active');
            $table->rememberToken();
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
        Schema::dropIfExists('drivers');
    }
}
