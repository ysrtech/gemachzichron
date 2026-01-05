<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== UPDATE ROTESSA PAYMENT METHODS ===\n\n";

// Find all Rotessa payment methods
$paymentMethods = \App\Models\PaymentMethod::where('gateway', 'Rotessa')->get();

echo "Found {$paymentMethods->count()} Rotessa payment methods\n\n";

$gateway = \App\Facades\Gateway::initialize('Rotessa');
$reflection = new ReflectionClass($gateway);
$method = $reflection->getMethod('get');
$method->setAccessible(true);

$updated = 0;
$skipped = 0;
$errors = 0;

foreach ($paymentMethods as $pm) {
    try {
        echo "Processing Payment Method #{$pm->id} (Customer: {$pm->gateway_identifier})...\n";
        
        // Fetch current data from Rotessa
        $response = $method->invoke($gateway, "customers/{$pm->gateway_identifier}");
        $customerData = $response->collect()->toArray();
        
        // Check if we need to update
        $currentData = $pm->gateway_data ?? [];
        $needsUpdate = false;
        
        // Check for missing fields
        if (empty($currentData['bank_name']) && !empty($customerData['bank_name'])) {
            $needsUpdate = true;
        }
        if (empty($currentData['account_number']) && !empty($customerData['account_number'])) {
            $needsUpdate = true;
        }
        if (empty($currentData['transit_number']) && !empty($customerData['transit_number'])) {
            $needsUpdate = true;
        }
        if (empty($currentData['institution_number']) && !empty($customerData['institution_number'])) {
            $needsUpdate = true;
        }
        
        if ($needsUpdate) {
            // Prepare updated gateway_data with masked sensitive info
            $updatedData = [
                'id' => $customerData['id'],
                'identifier' => $customerData['identifier'] ?? null,
                'customer_type' => $customerData['customer_type'] ?? null,
                'bank_name' => $customerData['bank_name'] ?? '',
            ];
            
            // Mask sensitive fields
            if (!empty($customerData['account_number'])) {
                $updatedData['account_number'] = substr_replace(
                    $customerData['account_number'], 
                    str_repeat('*', strlen($customerData['account_number']) - 2), 
                    0, 
                    -2
                );
            }
            
            if (!empty($customerData['transit_number'])) {
                $updatedData['transit_number'] = substr_replace(
                    $customerData['transit_number'], 
                    str_repeat('*', strlen($customerData['transit_number']) - 2), 
                    0, 
                    -2
                );
            }
            
            if (!empty($customerData['institution_number'])) {
                $updatedData['institution_number'] = substr_replace(
                    $customerData['institution_number'], 
                    str_repeat('*', strlen($customerData['institution_number']) - 2), 
                    0, 
                    -2
                );
            }
            
            $pm->update(['gateway_data' => $updatedData]);
            
            echo "  ✓ Updated - Bank: {$updatedData['bank_name']}, Account: " . ($updatedData['account_number'] ?? 'N/A') . "\n";
            $updated++;
        } else {
            echo "  - Skipped - Already has complete data\n";
            $skipped++;
        }
        
    } catch (\Exception $e) {
        echo "  ✗ Error: {$e->getMessage()}\n";
        $errors++;
    }
    
    echo "\n";
}

echo "=== SUMMARY ===\n";
echo "Total: {$paymentMethods->count()}\n";
echo "Updated: {$updated}\n";
echo "Skipped: {$skipped}\n";
echo "Errors: {$errors}\n";
