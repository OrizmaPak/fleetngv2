<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomerCheckoutsTable extends Migration
{
    public function up()
    {
        Schema::create('customer_checkouts', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 64)->unique();
            $table->unsignedBigInteger('customer_id')->index();
            $table->json('trip_amounts');
            $table->unsignedBigInteger('amount_minor');
            $table->string('currency', 3)->default('NGN');
            $table->string('status', 24)->default('pending');
            $table->string('transaction_id', 64)->nullable()->unique();
            $table->text('checkout_url')->nullable();
            $table->timestamps();
        });
    }

    public function down() { Schema::dropIfExists('customer_checkouts'); }
}
