<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOTPMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('otp_master', function (Blueprint $table) {
            $table->id();
            $table->string('appID');
            $table->string('name');
            $table->string('username');
            $table->string('OTP');
            $table->string('OTP_Type')->nullable();
            $table->timestamp('OTP_Start');
            $table->timestamp('OTP_End')->nullable();
            $table->string('IP');
            $table->string('status')->default('pending');
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
        Schema::dropIfExists('otp_master');
    }
}
