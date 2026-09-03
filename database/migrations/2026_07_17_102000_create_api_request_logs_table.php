<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiRequestLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_request_logs', function (Blueprint $table) {
            $table->id();
            $table->string('method');
            $table->string('path');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('source_app')->nullable();
            $table->string('user_email')->nullable();
            $table->integer('status_code');
            $table->float('duration', 8, 2); // duration in milliseconds
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
        Schema::dropIfExists('api_request_logs');
    }
}
