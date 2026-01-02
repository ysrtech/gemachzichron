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
        .note-content {
            background-color: white;
            padding: 20px;
            border-left: 4px solid #4A5568;
            margin: 20px 0;
            white-space: pre-wrap;
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
            <h1>New Message</h1>
        </div>
        <div class="content">
            <p>Shalom Uvracha {{ $member->first_name }} {{ $member->last_name }},</p>
            
            <p>A new message was sent to you by {{ $userName }}:</p>
            
            <div class="note-content">
                {{ $note->note }}
            </div>

            <p>Hatzlacha,<br>
            {{ config('app.name') }}</p>
        </div>
        <div class="footer">
            <p>This is an automated message. Please do not reply to this email.</p>
        </div>
    </div>
</body>
</html>