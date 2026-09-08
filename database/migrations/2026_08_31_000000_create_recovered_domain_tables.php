<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecoveredDomainTables extends Migration
{
    /**
     * Reconstructed from the application's Eloquent models and controllers.
     * The original archive did not contain migrations for these tables.
     */
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('email')->nullable()->unique();
            $table->string('phone_number', 32)->nullable()->unique();
            $table->string('country_code', 8)->nullable();
            $table->string('password')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->boolean('is_active')->default(true);
            $table->text('device_token')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('token', 512)->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('driver_id')->nullable()->index();
            $table->timestamp('last_activity')->nullable();
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('item_name');
            $table->decimal('item_cost', 14, 2)->default(0);
            $table->unsignedInteger('item_quantity')->default(1);
            $table->unsignedBigInteger('driver_id')->index();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('title')->nullable();
            $table->longText('contents')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->string('from')->nullable();
            $table->string('to')->nullable();
            $table->text('body')->nullable();
            $table->string('account_id')->nullable();
            $table->string('sms_id')->nullable()->index();
            $table->string('type')->nullable();
            $table->boolean('is_sent')->default(false);
            $table->text('error_message')->nullable();
            $table->timestamps();
        });

        Schema::create('trip_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedTinyInteger('status')->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('transaction_id')->nullable()->index();
            $table->string('payment_type')->nullable();
            $table->string('reference_code')->nullable()->index();
            $table->unsignedBigInteger('payment_confirmed_by')->nullable();
            $table->timestamps();
        });

        Schema::create('trip_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('trip_id')->index();
            $table->unsignedBigInteger('client_id')->nullable()->index();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('driver_id')->index();
            $table->boolean('is_read')->default(false);
            $table->unsignedTinyInteger('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('draft_trips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('driver_id')->nullable()->index();
            $table->unsignedBigInteger('client_id')->index();
            $table->unsignedBigInteger('pickup_location_id')->nullable()->index();
            $table->unsignedBigInteger('drop_location_id')->nullable()->index();
            $table->integer('total_cost')->nullable();
            $table->string('client_name')->nullable();
            $table->dateTime('pick_up_datetime')->nullable();
            $table->unsignedTinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('draft_trips');
        Schema::dropIfExists('trip_requests');
        Schema::dropIfExists('trip_payments');
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('customers');
    }
}
