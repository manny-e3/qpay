<?php

use App\Models\AppConfig;
use App\Models\PaymentGateway;
use App\Models\AppPaymentGateway;

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$appConfig = AppConfig::first();
$gateway = PaymentGateway::where('slug', 'paystack')->first();

if ($appConfig && $gateway) {
    AppPaymentGateway::updateOrCreate(
        ['app_config_id' => $appConfig->id, 'payment_gateway_id' => $gateway->id],
        ['config' => $gateway->config, 'is_active' => true]
    );
    echo "Successfully linked app [{$appConfig->appName}] to Paystack gateway.\n";
} else {
    echo "Failed to link app. Check if apps and gateways exist.\n";
}
