<?php

namespace App\Services\Payment;

use App\Models\PaymentTransaction;

interface PaymentServiceInterface
{
    /**
     * Initialize a transaction with the gateway.
     *
     * @param PaymentTransaction $transaction
     * @param array $config Gateway configuration
     * @return array ['checkout_url' => '...', 'reference' => '...']
     */
    public function initializeTransaction(PaymentTransaction $transaction, array $config);

    /**
     * Verify a transaction with the gateway.
     *
     * @param string $reference
     * @param array $config Gateway configuration
     * @return array ['status' => 'successful|failed|pending', 'gateway_response' => [...]]
     */
    public function verifyTransaction(string $reference, array $config);
}
