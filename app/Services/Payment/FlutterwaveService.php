<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;
use Illuminate\Support\Facades\Http;
use Exception;

class FlutterwaveService implements PaymentServiceInterface
{
    protected $baseUrl = 'https://api.flutterwave.com/v3';

    public function initializeTransaction(PaymentTransaction $transaction, array $config)
    {
        $secretKey = $config['secret_key']
            ?? $config['secret']
            ?? $config['flutterwave_secret_key']
            ?? config('services.flutterwave.secret_key')
            ?? env('FLUTTERWAVE_SECRET_KEY')
            ?? env('FLUTTERWAVE_SECRET');

        if (!$secretKey) {
            throw new Exception("Flutterwave secret key is missing in configuration. Please set FLUTTERWAVE_SECRET_KEY in your .env file or update gateway config in backend.");
        }

        $response = Http::withToken($secretKey)->post($this->baseUrl . '/payments', [
            'tx_ref' => $transaction->reference,
            'amount' => $transaction->amount,
            'currency' => $transaction->currency ?? 'NGN',
            'redirect_url' => route('api.payment.callback', ['gateway' => 'flutterwave']),
            'customer' => [
                'email' => $transaction->customer_email,
                'name' => $transaction->metadata['customer_name'] ?? 'Customer',
            ],
            'customizations' => [
                'title' => $transaction->app->appName,
                'description' => 'Payment for ' . ($transaction->metadata['product'] ?? 'Order'),
            ],
        ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'checkout_url' => $data['data']['link'],
                'reference' => $transaction->reference,
            ];
        }

        throw new Exception("Flutterwave initialization failed: " . $response->body());
    }

    public function verifyTransaction(string $reference, array $config)
    {
        $secretKey = $config['secret_key']
            ?? $config['secret']
            ?? $config['flutterwave_secret_key']
            ?? config('services.flutterwave.secret_key')
            ?? env('FLUTTERWAVE_SECRET_KEY')
            ?? env('FLUTTERWAVE_SECRET');

        if (!$secretKey) {
            throw new Exception("Flutterwave secret key is missing in configuration. Please set FLUTTERWAVE_SECRET_KEY in your .env file or update gateway config in backend.");
        }

        // Flutterwave v3 verification by tx_ref
        $response = Http::withToken($secretKey)->get($this->baseUrl . "/transactions/verify_by_reference", [
            'tx_ref' => $reference
        ]);

        if ($response->successful()) {
            $data = $response->json();
            $status = 'pending';
            
            if ($data['data']['status'] === 'successful') {
                $status = 'successful';
            } elseif (in_array($data['data']['status'], ['failed', 'cancelled'])) {
                $status = 'failed';
            }

            return [
                'status' => $status,
                'gateway_response' => $data['data'],
            ];
        }

        throw new Exception("Flutterwave verification failed: " . $response->body());
    }
}
