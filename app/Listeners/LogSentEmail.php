<?php

namespace App\Listeners;

use App\Models\MailLog;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class LogSentEmail
{
    public function handle(MessageSent $event)
    {
        $message = $event->message;
        
        // Get recipient
        $to = $message->getTo();
        $toEmail = array_key_first($to);
        $toName = $to[$toEmail] ?? null;
        
        // Get sender
        $from = $message->getFrom();
        $fromEmail = array_key_first($from);
        $fromName = $from[$fromEmail] ?? null;
        
        // Get message ID from headers
        $headers = $message->getHeaders();
        $messageId = null;
        
        try {
            if ($headers->has('X-Mailgun-Message-Id')) {
                $messageId = $headers->get('X-Mailgun-Message-Id')->getFieldBody();
            } elseif ($headers->has('Message-ID')) {
                $messageId = $headers->get('Message-ID')->getFieldBody();
            }
        } catch (\Exception $e) {
            // Ignore header errors
        }
        
        MailLog::create([
            'message_id' => $messageId,
            'to_email' => $toEmail,
            'to_name' => $toName,
            'from_email' => $fromEmail,
            'from_name' => $fromName,
            'subject' => $message->getSubject(),
            'body' => $message->getBody(),
            'status' => MailLog::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }
}
