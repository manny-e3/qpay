<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOTPHistoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_history', function (Blueprint $table) {
            $table->id();
            $table->string('appID');
            $table->string('username');
            $table->string('OTP');
            $table->timestamp('OTP_Start');
            $table->timestamp('OTP_End')->nullable();
            $table->string('IP');
            $table->string('status');
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
        Schema::dropIfExists('otp_history');
    }
}
