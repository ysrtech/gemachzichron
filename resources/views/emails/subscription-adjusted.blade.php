<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #4A5568;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f7fafc;
            padding: 30px;
            border: 1px solid #e2e8f0;
            border-top: none;
            border-radius: 0 0 5px 5px;
        }
        .highlight {
            background-color: #fef5e7;
            padding: 15px;
            border-left: 4px solid #f39c12;
            margin: 20px 0;
        }
        .amount-change {
            font-size: 18px;
            font-weight: bold;
            color: #2c3e50;
            text-align: center;
            margin: 20px 0;
        }
        .old-amount {
            text-decoration: line-through;
            color: #e74c3c;
        }
        .new-amount {
            color: #27ae60;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Subscription Payment Update</h1>
        </div>
        <div class="content">
            <p>Dear {{ $member->first_name }} {{ $member->last_name }},</p>
            
            <p>This is to notify you that your monthly loan payment subscription has been adjusted.</p>
            
            <div class="highlight">
                <strong>Loan Details:</strong>
                <ul>
                    <li><strong>Loan Date:</strong> {{ \Carbon\Carbon::parse($subscription->paysLoan->loan_date)->format('F d, Y') }}</li>
                    @if($subscription->paysLoan->dependent)
                        <li><strong>Child:</strong> {{ $subscription->paysLoan->dependent->name }}</li>
                    @endif
                    <li><strong>Loan Amount:</strong> ${{ number_format($subscription->paysLoan->amount, 2) }}</li>
                    <li><strong>Current Balance:</strong> ${{ number_format($subscription->paysLoan->remaining_balance, 2) }}</li>
                </ul>
            </div>

            <div class="amount-change">
                <div style="margin-bottom: 10px;">
                    <span style="color: #7f8c8d;">Previous monthly payment:</span>
                    <span class="old-amount">${{ number_format($oldAmount, 2) }}</span>
                </div>
                <div style="margin-bottom: 10px;">
                    <span style="color: #7f8c8d;">New monthly payment:</span>
                    <span class="new-amount">${{ number_format($newAmount, 2) }}</span>
                </div>
                @if($subscription->paymentMethod && $subscription->gateway === 'Cardknox' && isset($subscription->paymentMethod->gateway_data['Issuer']) && isset($subscription->paymentMethod->gateway_data['MaskedCardNumber']))
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                    <span style="color: #7f8c8d;">Account:</span>
                    <strong>{{ $subscription->paymentMethod->gateway_data['Issuer'] }} {{ $subscription->paymentMethod->gateway_data['MaskedCardNumber'] }}</strong>
                </div>
                @elseif($subscription->paymentMethod && $subscription->gateway === 'Rotessa' && isset($subscription->paymentMethod->gateway_data['account_number']))
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0;">
                    <span style="color: #7f8c8d;">Account:</span>
                    <strong>@if(!empty($subscription->paymentMethod->gateway_data['bank_name'])){{ $subscription->paymentMethod->gateway_data['bank_name'] }} - @endif{{ $subscription->paymentMethod->gateway_data['account_number'] }}</strong>
                </div>
                @endif
            </div>

            <p><strong>This new amount will take effect starting from your next scheduled payment.</strong></p>

            <p>If you have any questions or concerns about this change, please don't hesitate to contact us by replying to this email.</p>

            <p>Hatzlacha and Best regards,<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>This is an automated message.</p>
        </div>
    </div>
</body>
</html>
