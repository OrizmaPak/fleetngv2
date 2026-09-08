<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('token')->nullable();
            $table->string('password')->nullable();
            $table->string('phone', 32)->nullable();
            $table->string('photo')->nullable();
            $table->string('vehicle_id')->nullable();
            $table->unsignedInteger('auth_pin')->nullable();
            $table->string('merchant_assigned')->nullable();
            $table->string('merchant_id')->nullable();
            $table->string('merchant_name')->nullable();
            $table->string('merchant_address')->nullable();
            $table->string('payment_user')->nullable();
            $table->dateTime('last_active')->nullable();
            $table->unsignedTinyInteger('user_type')->default(3)->comment('1: super admin, 2: admin, 3: company user, 4: merchant, 5: payment user');
            $table->boolean('is_active')->default(true)->comment('0: inactive, 1: active');
            $table->boolean('is_payment_user')->default(false);
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
        Schema::dropIfExists('users');
    }
}
