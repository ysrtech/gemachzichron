<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// CONFIGURATION - Update these values
$rotessaCustomerId = '244799'; // Enter the Rotessa customer ID here
$memberId = '324'; // Enter your system's member ID here

if (empty($rotessaCustomerId) || empty($memberId)) {
    echo "ERROR: Please set both rotessaCustomerId and memberId at the top of this script.\n";
    exit(1);
}

try {
    // Fetch customer from Rotessa
    echo "Fetching customer $rotessaCustomerId from Rotessa...\n";
    $gateway = \App\Facades\Gateway::initialize('Rotessa');
    
    // Use reflection to access protected method
    $reflection = new ReflectionClass($gateway);
    $method = $reflection->getMethod('get');
    $method->setAccessible(true);
    $response = $method->invoke($gateway, "customers/$rotessaCustomerId");
    
    $customerData = $response->collect()->toArray();
    
    echo "Found customer: {$customerData['name']} ({$customerData['email']})\n";
    echo "Bank: {$customerData['bank_name']}\n";
    echo "Account: {$customerData['account_number']}\n\n";
    
    // Check if member exists
    $member = \App\Models\Member::find($memberId);
    if (!$member) {
        echo "ERROR: Member #$memberId not found in system.\n";
        exit(1);
    }
    
    echo "Member found: {$member->first_name} {$member->last_name}\n";
    
    // Check if payment method already exists
    $existingPM = \App\Models\PaymentMethod::where('gateway', 'Rotessa')
        ->where('gateway_identifier', $rotessaCustomerId)
        ->first();
    
    if ($existingPM) {
        echo "WARNING: Payment method already exists in system (ID: {$existingPM->id})\n";
        echo "Updating existing payment method...\n";
        
        $existingPM->update([
            'member_id' => $memberId,
            'gateway_data' => [
                'id' => $customerData['id'],
                'identifier' => $customerData['identifier'] ?? null,
                'customer_type' => $customerData['customer_type'] ?? null,
                'bank_name' => $customerData['bank_name'] ?? null,
                'account_number' => isset($customerData['account_number']) 
                    ? substr_replace($customerData['account_number'], str_repeat('*', strlen($customerData['account_number']) - 2), 0, -2)
                    : null,
                'transit_number' => isset($customerData['transit_number'])
                    ? substr_replace($customerData['transit_number'], str_repeat('*', strlen($customerData['transit_number']) - 2), 0, -2)
                    : null,
                'institution_number' => isset($customerData['institution_number'])
                    ? substr_replace($customerData['institution_number'], str_repeat('*', strlen($customerData['institution_number']) - 2), 0, -2)
                    : null,
            ]
        ]);
        
        echo "SUCCESS: Updated payment method #{$existingPM->id}\n";
    } else {
        // Create new payment method
        echo "Creating new payment method...\n";
        
        $paymentMethod = \App\Models\PaymentMethod::create([
            'member_id' => $memberId,
            'gateway' => 'Rotessa',
            'gateway_identifier' => $rotessaCustomerId,
            'gateway_data' => [
                'id' => $customerData['id'],
                'identifier' => $customerData['identifier'] ?? null,
                'customer_type' => $customerData['customer_type'] ?? null,
                'bank_name' => $customerData['bank_name'] ?? null,
                'account_number' => isset($customerData['account_number']) 
                    ? substr_replace($customerData['account_number'], str_repeat('*', strlen($customerData['account_number']) - 2), 0, -2)
                    : null,
                'transit_number' => isset($customerData['transit_number'])
                    ? substr_replace($customerData['transit_number'], str_repeat('*', strlen($customerData['transit_number']) - 2), 0, -2)
                    : null,
                'institution_number' => isset($customerData['institution_number'])
                    ? substr_replace($customerData['institution_number'], str_repeat('*', strlen($customerData['institution_number']) - 2), 0, -2)
                    : null,
            ]
        ]);
        
        echo "SUCCESS: Created payment method #{$paymentMethod->id}\n";
    }
    
    echo "\nDone!\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
