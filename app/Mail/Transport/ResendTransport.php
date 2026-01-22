<?php

namespace App\Mail\Transport;

use Resend;
use Illuminate\Mail\Transport\Transport;
use Swift_Mime_SimpleMessage;

class ResendTransport extends Transport
{
    protected $resend;

    public function __construct(string $apiKey)
    {
        $this->resend = Resend::client($apiKey);
    }

    public function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->beforeSendPerformed($message);

        $to = collect($this->getTo($message))
            ->map(fn($display, $address) => $display ? "{$display} <{$address}>" : $address)
            ->values()
            ->toArray();

        $from = collect($message->getFrom())
            ->map(fn($display, $address) => $display ? "{$display} <{$address}>" : $address)
            ->first();

        $payload = [
            'from' => $from,
            'to' => $to,
            'subject' => $message->getSubject(),
        ];

        // Add HTML body
        if ($message->getBody()) {
            $payload['html'] = $message->getBody();
        }

        // Add text body if available
        foreach ($message->getChildren() as $child) {
            if ($child->getContentType() === 'text/plain') {
                $payload['text'] = $child->getBody();
            }
        }

        // Add reply-to if set
        if ($replyTo = $message->getReplyTo()) {
            $payload['reply_to'] = collect($replyTo)
                ->map(fn($display, $address) => $display ? "{$display} <{$address}>" : $address)
                ->first();
        }

        // Add CC if set
        if ($cc = $message->getCc()) {
            $payload['cc'] = collect($cc)
                ->map(fn($display, $address) => $display ? "{$display} <{$address}>" : $address)
                ->values()
                ->toArray();
        }

        // Add BCC if set
        if ($bcc = $message->getBcc()) {
            $payload['bcc'] = collect($bcc)
                ->map(fn($display, $address) => $display ? "{$display} <{$address}>" : $address)
                ->values()
                ->toArray();
        }

        // Add attachments if any
        $attachments = [];
        foreach ($message->getChildren() as $child) {
            if ($child instanceof \Swift_Mime_Attachment) {
                $attachments[] = [
                    'filename' => $child->getFilename(),
                    'content' => base64_encode($child->getBody()),
                ];
            }
        }

        if (!empty($attachments)) {
            $payload['attachments'] = $attachments;
        }

        try {
            $response = $this->resend->emails->send($payload);
            
            // Store the Resend message ID in the headers for logging
            if (isset($response->id)) {
                $message->getHeaders()->addTextHeader('X-Resend-Message-Id', $response->id);
                $message->getHeaders()->addTextHeader('Message-ID', $response->id);
            }
            
            $this->sendPerformed($message);
            
            return $this->numberOfRecipients($message);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    protected function getTo(Swift_Mime_SimpleMessage $message)
    {
        return $message->getTo();
    }
}
