<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppPaymentGatewaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_config_id');
            $table->unsignedBigInteger('payment_gateway_id');
            $table->json('config');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('app_config_id')->references('id')->on('app_config')->onDelete('cascade');
            $table->foreign('payment_gateway_id')->references('id')->on('payment_gateways')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_payment_gateways');
    }
}
