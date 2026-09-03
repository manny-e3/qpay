<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Exception;

class PaystackService implements PaymentServiceInterface
{
    protected $baseUrl = 'https://api.paystack.co';

    public function initializeTransaction(PaymentTransaction $transaction, array $config)
    {
        $secretKey = $config['secret_key']
            ?? $config['secret']
            ?? $config['paystack_secret_key']
            ?? config('services.paystack.secret_key')
            ?? env('PAYSTACK_SECRET_KEY')
            ?? env('PAYSTACK_SECRET');

        if (!$secretKey) {
            throw new Exception("Paystack secret key is missing in configuration. Please set PAYSTACK_SECRET_KEY in your .env file or update gateway config in backend.");
        }

        $response = Http::withToken($secretKey)->post($this->baseUrl . '/transaction/initialize', [
            'amount' => $transaction->amount * 100, // Paystack expects amount in kobo
            'email' => $transaction->customer_email,
            'reference' => $transaction->reference,
            'callback_url' => route('api.payment.callback', ['gateway' => 'paystack']),
            'metadata' => $transaction->metadata,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'checkout_url' => $data['data']['authorization_url'],
                'reference' => $data['data']['reference'],
            ];
        }

        throw new Exception("Paystack initialization failed: " . $response->body());
    }

    public function verifyTransaction(string $reference, array $config)
    {
        $secretKey = $config['secret_key']
            ?? $config['secret']
            ?? $config['paystack_secret_key']
            ?? config('services.paystack.secret_key')
            ?? env('PAYSTACK_SECRET_KEY')
            ?? env('PAYSTACK_SECRET');

        if (!$secretKey) {
            throw new Exception("Paystack secret key is missing in configuration. Please set PAYSTACK_SECRET_KEY in your .env file or update gateway config in backend.");
        }

        $response = Http::withToken($secretKey)->get($this->baseUrl . "/transaction/verify/{$reference}");

        if ($response->successful()) {
            $data = $response->json();
            $status = 'pending';
            
            if ($data['data']['status'] === 'success') {
                $status = 'successful';
            } elseif (in_array($data['data']['status'], ['failed', 'reversed'])) {
                $status = 'failed';
            }

            return [
                'status' => $status,
                'gateway_response' => $data['data'],
            ];
        }

        throw new Exception("Paystack verification failed: " . $response->body());
    }
}
