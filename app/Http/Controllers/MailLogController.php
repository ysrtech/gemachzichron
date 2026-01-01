<?php

namespace App\Http\Controllers;

use App\Models\MailLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MailLogController extends Controller
{
    public function index(Request $request)
    {
        $query = MailLog::query();

        // Filter by email
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('to_email', 'like', "%{$request->search}%")
                  ->orWhere('subject', 'like', "%{$request->search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->whereDate('sent_at', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('sent_at', '<=', $request->to_date);
        }

        $mailLogs = $query->latest('sent_at')->paginate(20);

        return Inertia::render('MailLogs/Index', [
            'filters' => $request->all(['search', 'status', 'from_date', 'to_date']),
            'mailLogs' => $mailLogs,
        ]);
    }

    public function show(MailLog $mailLog)
    {
        return Inertia::render('MailLogs/Show', [
            'mailLog' => $mailLog,
        ]);
    }

    // Webhook handler for Mailgun events
    public function webhook(Request $request)
    {
        $eventData = $request->input('event-data');
        
        if (!$eventData) {
            return response()->json(['message' => 'Invalid webhook data'], 400);
        }

        $messageId = $eventData['message']['headers']['message-id'] ?? null;
        $event = $eventData['event'] ?? null;

        if (!$messageId) {
            return response()->json(['message' => 'No message ID'], 400);
        }

        $mailLog = MailLog::where('message_id', $messageId)->first();

        if (!$mailLog) {
            return response()->json(['message' => 'Mail log not found'], 404);
        }

        switch ($event) {
            case 'delivered':
                $mailLog->update([
                    'status' => MailLog::STATUS_DELIVERED,
                    'delivered_at' => now(),
                ]);
                break;

            case 'opened':
                $mailLog->increment('open_count');
                $mailLog->update([
                    'status' => MailLog::STATUS_OPENED,
                    'opened_at' => $mailLog->opened_at ?? now(),
                ]);
                break;

            case 'clicked':
                $mailLog->increment('click_count');
                $mailLog->update([
                    'status' => MailLog::STATUS_CLICKED,
                    'clicked_at' => $mailLog->clicked_at ?? now(),
                ]);
                break;

            case 'failed':
            case 'bounced':
                $mailLog->update([
                    'status' => $event === 'bounced' ? MailLog::STATUS_BOUNCED : MailLog::STATUS_FAILED,
                    'failed_at' => now(),
                    'error_message' => $eventData['delivery-status']['message'] ?? 'Unknown error',
                ]);
                break;

            case 'complained':
                $mailLog->update([
                    'status' => MailLog::STATUS_COMPLAINED,
                ]);
                break;
        }

        return response()->json(['message' => 'Webhook processed']);
    }
}
