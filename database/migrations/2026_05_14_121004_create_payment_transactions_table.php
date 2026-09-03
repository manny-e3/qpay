<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_config_id');
            $table->unsignedBigInteger('payment_gateway_id')->nullable();
            $table->string('reference')->unique();
            $table->decimal('amount', 20, 2);
            $table->string('currency')->default('NGN');
            $table->string('status')->default('pending'); // pending, successful, failed, reversed
            $table->string('customer_email');
            $table->string('callback_url')->nullable();
            $table->json('metadata')->nullable();
            $table->json('gateway_response')->nullable();
            $table->timestamps();

            $table->foreign('app_config_id')->references('id')->on('app_config')->onDelete('cascade');
            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payment_transactions');
    }
}
