<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentGatewaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\PaymentGateway::updateOrCreate(
            ['slug' => 'paystack'],
            [
                'name' => 'Paystack',
                'description' => 'Accept payments from anyone, anywhere in Africa.',
                'is_active' => true
            ]
        );

        \App\Models\PaymentGateway::updateOrCreate(
            ['slug' => 'flutterwave'],
            [
                'name' => 'Flutterwave',
                'description' => 'The easiest way to make and accept payments from customers anywhere in the world.',
                'is_active' => false // Set to false since implementation is not ready
            ]
        );
    }
}
