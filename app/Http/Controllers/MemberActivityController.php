<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Note;
use App\Models\Loan;
use App\Mail\NoteNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class MemberActivityController extends Controller
{
    public function index(Member $member)
    {
        // Get all activities for this member
        $activities = Activity::query()
            ->where('member_id', $member->id)
            ->with(['causer', 'subject'])
            ->latest()
            ->paginate(20);

        $notes = $member->notes()->latest()->get();

        return Inertia::render('Members/Activities/Index', [
            'member' => $member,
            'activities' => $activities,
            'notes' => $notes,
        ]);
    }

    public function store(Request $request, Member $member)
    {
        \Log::info('Note store request received', [
            'all_data' => $request->all(),
            'email_to_member' => $request->input('email_to_member'),
            'has_email_to_member' => $request->has('email_to_member'),
        ]);

        $request->validate([
            'note' => 'required|string|max:5000',
            'email_to_member' => 'nullable|boolean',
        ]);

        $note = $member->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->note,
            'email_sent' => false,
        ]);

        // Send email if requested and member has email
        $shouldEmail = $request->input('email_to_member', false);
        
        \Log::info('Email sending check', [
            'should_email' => $shouldEmail,
            'member_has_email' => !empty($member->email),
            'member_email' => $member->email
        ]);
        
        if ($shouldEmail && $member->email) {
            try {
                \Log::info('Attempting to send note notification email', [
                    'member_id' => $member->id,
                    'member_email' => $member->email,
                    'note_id' => $note->id
                ]);
                
                Mail::to($member->email)->send(
                    new NoteNotification($member, $note, auth()->user()->name)
                );
                
                // Mark note as emailed
                $note->update(['email_sent' => true]);
                
                \Log::info('Note notification email sent successfully');
            } catch (\Exception $e) {
                \Log::error('Failed to send note notification email: ' . $e->getMessage(), [
                    'exception' => $e,
                    'trace' => $e->getTraceAsString()
                ]);
                // Don't fail the request if email fails
            }
        }

        return redirect()->back()->with('success', 'Note added successfully');
    }

    public function update(Request $request, Member $member, Note $note)
    {
        if ($note->member_id !== $member->id) {
            abort(404);
        }

        $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        $note->update([
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Note updated successfully');
    }

    public function destroy(Member $member, Note $note)
    {
        if ($note->member_id !== $member->id) {
            abort(404);
        }

        $note->delete();

        return redirect()->back()->with('success', 'Note deleted successfully');
    }
}
