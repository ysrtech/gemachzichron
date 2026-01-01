<?php

namespace App\Console\Commands;

use App\Models\MailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncMailgunEvents extends Command
{
    protected $signature = 'mailgun:sync-events';
    protected $description = 'Sync email events from Mailgun API';

    public function handle()
    {
        $domain = config('services.mailgun.domain');
        $secret = config('services.mailgun.secret');
        $endpoint = config('services.mailgun.endpoint', 'api.mailgun.net');

        $this->info('Fetching events from Mailgun...');

        // Fetch events from last 24 hours
        $response = Http::withBasicAuth('api', $secret)
            ->get("https://{$endpoint}/v3/{$domain}/events", [
                'begin' => now()->subDay()->timestamp,
                'ascending' => 'yes',
                'limit' => 300
            ]);

        if (!$response->successful()) {
            $this->error('Failed to fetch events from Mailgun');
            return 1;
        }

        $events = $response->json('items', []);
        $updated = 0;

        foreach ($events as $event) {
            $messageId = $event['message']['headers']['message-id'] ?? null;
            
            if (!$messageId) {
                continue;
            }

            // Strip angle brackets from message ID
            $messageId = trim($messageId, '<>');

            $mailLog = MailLog::where('message_id', $messageId)
                ->orWhere('message_id', '<' . $messageId . '>')
                ->first();

            if (!$mailLog) {
                $this->warn("No mail log found for message ID: {$messageId}");
                continue;
            }

            $eventType = $event['event'] ?? null;
            $timestamp = $event['timestamp'] ?? null;

            $this->info("Processing {$eventType} event for email: {$mailLog->to_email}");

            switch ($eventType) {
                case 'delivered':
                    if (!$mailLog->delivered_at) {
                        $mailLog->update([
                            'status' => MailLog::STATUS_DELIVERED,
                            'delivered_at' => $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : now(),
                        ]);
                        $updated++;
                    }
                    break;

                case 'opened':
                    $mailLog->increment('open_count');
                    $mailLog->update([
                        'status' => MailLog::STATUS_OPENED,
                        'opened_at' => $mailLog->opened_at ?? ($timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : now()),
                    ]);
                    $updated++;
                    break;

                case 'clicked':
                    $mailLog->increment('click_count');
                    $mailLog->update([
                        'status' => MailLog::STATUS_CLICKED,
                        'clicked_at' => $mailLog->clicked_at ?? ($timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : now()),
                    ]);
                    $updated++;
                    break;

                case 'failed':
                case 'bounced':
                    $mailLog->update([
                        'status' => $eventType === 'bounced' ? MailLog::STATUS_BOUNCED : MailLog::STATUS_FAILED,
                        'failed_at' => $timestamp ? \Carbon\Carbon::createFromTimestamp($timestamp) : now(),
                        'error_message' => $event['delivery-status']['message'] ?? $event['reason'] ?? 'Unknown error',
                    ]);
                    $updated++;
                    break;

                case 'complained':
                    $mailLog->update([
                        'status' => MailLog::STATUS_COMPLAINED,
                    ]);
                    $updated++;
                    break;
            }
        }

        $this->info("Synced {$updated} email status updates from " . count($events) . " events.");
        return 0;
    }
}
