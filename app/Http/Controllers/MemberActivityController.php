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
        // Get all activities for this member
        $activities = Activity::query()
            ->where('member_id', $member->id)
            ->with(['causer', 'subject'])
            ->latest()
            ->paginate(5);

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
