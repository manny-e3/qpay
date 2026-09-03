<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminEmailToAppConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app_config', function (Blueprint $table) {
            $table->string('admin_email')->nullable()->after('password');
            $table->integer('otp_length')->default(4)->after('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app_config', function (Blueprint $table) {
            $table->dropColumn(['admin_email', 'otp_length']);
        });
    }
}
