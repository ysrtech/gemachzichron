<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Note;
use App\Models\Loan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class MemberActivityController extends Controller
{
    public function index(Member $member)
    {
        // Get activities for the member and related models
        $activities = Activity::query()
            ->where(function ($query) use ($member) {
                // Activities where member is the subject
                $query->where(function ($q) use ($member) {
                    $q->where('subject_type', Member::class)
                      ->where('subject_id', $member->id);
                })
                // Activities on member's loans (including deleted)
                ->orWhere(function ($q) use ($member) {
                    $q->where('subject_type', 'App\\Models\\Loan')
                      ->whereIn('subject_id', Loan::withTrashed()->where('member_id', $member->id)->pluck('id'));
                })
                // Activities on member's subscriptions
                ->orWhere(function ($q) use ($member) {
                    $q->where('subject_type', 'App\\Models\\Subscription')
                      ->whereIn('subject_id', $member->subscriptions()->pluck('id'));
                })
                // Activities on member's transactions
                ->orWhere(function ($q) use ($member) {
                    $q->where('subject_type', 'App\\Models\\Transaction')
                      ->whereIn('subject_id', $member->transactions()->pluck('id'));
                });
            })
            ->with(['causer', 'subject'])
            ->latest()
            ->paginate(50);

        $notes = $member->notes()->latest()->get();

        return Inertia::render('Members/Activities/Index', [
            'member' => $member,
            'activities' => $activities,
            'notes' => $notes,
        ]);
    }

    public function store(Request $request, Member $member)
    {
        $request->validate([
            'note' => 'required|string|max:5000',
        ]);

        $member->notes()->create([
            'user_id' => auth()->id(),
            'note' => $request->note,
        ]);

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
