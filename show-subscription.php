<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$s = App\Models\Subscription::with('member', 'paysLoan.dependent', 'paymentMethod')->find(2149);

if ($s) {
    echo "=== MEMBER ===\n";
    echo "Name: {$s->member->first_name} {$s->member->last_name}\n";
    echo "Email: {$s->member->email}\n\n";
    
    echo "=== SUBSCRIPTION ===\n";
    echo "ID: {$s->id}\n";
    echo "Amount: \${$s->amount}\n";
    echo "Gateway: {$s->gateway}\n";
    echo "Payment Method ID: " . ($s->payment_method_id ?? 'NULL') . "\n\n";
    
    if ($s->paysLoan) {
        echo "=== LOAN ===\n";
        echo "Amount: \${$s->paysLoan->amount}\n";
        echo "Remaining Balance: \${$s->paysLoan->remaining_balance}\n";
        echo "Loan Date: {$s->paysLoan->loan_date}\n";
        if ($s->paysLoan->dependent) {
            echo "Dependent: {$s->paysLoan->dependent->name}\n";
        }
        echo "\n";
    }
    
    echo "=== MEMBER'S PAYMENT METHODS ===\n";
    $paymentMethods = App\Models\PaymentMethod::where('member_id', $s->member_id)->get();
    if ($paymentMethods->count() > 0) {
        foreach ($paymentMethods as $pm) {
            echo "- ID: {$pm->id}, Gateway: {$pm->gateway}, Identifier: {$pm->gateway_identifier}\n";
            if ($pm->gateway_data) {
                echo "  Data: " . json_encode($pm->gateway_data) . "\n";
            }
        }
    } else {
        echo "No payment methods found for this member\n";
    }
    echo "\n";
    
    if ($s->paymentMethod) {
        echo "=== LINKED PAYMENT METHOD ===\n";
        echo "Gateway: {$s->paymentMethod->gateway}\n";
        echo "Gateway Data:\n";
        print_r($s->paymentMethod->gateway_data);
    } else {
        echo "=== NO PAYMENT METHOD LINKED TO THIS SUBSCRIPTION ===\n";
    }
} else {
    echo "Subscription 2149 not found\n";
}
