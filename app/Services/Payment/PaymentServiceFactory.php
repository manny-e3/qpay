<?php

namespace App\Services\Payment;

use Exception;

class PaymentServiceFactory
{
    public static function make(string $slug): PaymentServiceInterface
    {
        switch ($slug) {
            case 'paystack':
                return new PaystackService();
            case 'flutterwave':
                return new FlutterwaveService();
            default:
                throw new Exception("Payment gateway [{$slug}] is not supported.");
        }
    }
}
