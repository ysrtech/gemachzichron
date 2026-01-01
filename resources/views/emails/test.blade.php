<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h1 style="color: #2563eb;">Test Email</h1>
        <p>This is a test email sent at {{ now()->format('F j, Y g:i A') }}.</p>
        <p>If you're receiving this email, your mail system is configured correctly!</p>
        
        <div style="background-color: #f3f4f6; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <h2 style="margin-top: 0; color: #1f2937;">Email Tracking</h2>
            <p>This email includes tracking for:</p>
            <ul>
                <li>Email delivery</li>
                <li>Opens (when you view this email)</li>
                <li>Clicks (if you click any links)</li>
            </ul>
        </div>

        <p>
            <a href="{{ config('app.url') }}" style="display: inline-block; padding: 10px 20px; background-color: #2563eb; color: white; text-decoration: none; border-radius: 5px;">
                Click here to test link tracking
            </a>
        </p>

        <p style="color: #6b7280; font-size: 12px; margin-top: 30px;">
            Sent from {{ config('app.name') }}
        </p>
    </div>
</body>
</html>
