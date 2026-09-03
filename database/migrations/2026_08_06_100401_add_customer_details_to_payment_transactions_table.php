<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerDetailsToPaymentTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->string('customer_first_name')->nullable()->after('customer_email');
            $table->string('customer_last_name')->nullable()->after('customer_first_name');
            $table->string('customer_phone')->nullable()->after('customer_last_name');
            $table->string('customer_company')->nullable()->after('customer_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropColumn(['customer_first_name', 'customer_last_name', 'customer_phone', 'customer_company']);
        });
    }
}
