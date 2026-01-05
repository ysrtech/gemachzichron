<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Get a sample payment method
$pm = \App\Models\PaymentMethod::where('gateway', 'Rotessa')->first();

if (!$pm) {
    echo "No Rotessa payment methods found\n";
    exit;
}

echo "Checking Payment Method #{$pm->id} (Customer: {$pm->gateway_identifier})\n\n";

// Fetch from Rotessa
$gateway = \App\Facades\Gateway::initialize('Rotessa');
$reflection = new ReflectionClass($gateway);
$method = $reflection->getMethod('get');
$method->setAccessible(true);

$response = $method->invoke($gateway, "customers/{$pm->gateway_identifier}");
$customerData = $response->collect()->toArray();

echo "=== RAW ROTESSA DATA ===\n";
print_r($customerData);

echo "\n\n=== CURRENT gateway_data IN DATABASE ===\n";
print_r($pm->gateway_data);
