<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\Mail;

try {
    $logger = new \Swift_Plugins_Loggers_ArrayLogger();
    Mail::getSwiftMailer()->registerPlugin(new \Swift_Plugins_LoggerPlugin($logger));

    Mail::raw('Test email from OTP microservice', function ($message) {
        $message->to('aboajah.emmanuel@gmail.com')
                ->subject('SMTP Test');
    });
    echo "Email sent successfully!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    if (isset($logger)) {
        echo "SMTP Log:\n" . $logger->dump() . "\n";
    }
}
