<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropAppConfigForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_payment_gateways', function (Blueprint $table) {
            $table->dropForeign(['app_config_id']);
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropForeign(['app_config_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_payment_gateways', function (Blueprint $table) {
            $table->foreign('app_config_id')->references('id')->on('app_config')->onDelete('cascade');
        });

        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->foreign('app_config_id')->references('id')->on('app_config')->onDelete('cascade');
        });
    }
}
