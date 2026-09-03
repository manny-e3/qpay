<?php
$env = file_get_contents(__DIR__ . '/.env');
preg_match('/MAIL_PASSWORD=(.*)/', $env, $matches);
echo "Raw password from .env: " . ($matches[1] ?? 'Not found') . "\n";

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Parsed MAIL_PASSWORD: " . config('mail.mailers.smtp.password') . "\n";
echo "Parsed MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "Parsed MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
