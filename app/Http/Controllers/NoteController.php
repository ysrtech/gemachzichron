<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;

class NoteController extends Controller
{
    public function store(Request $request, $noteableType, $noteableId)
    {
        Note::fromNoteableInstance($noteableType, $noteableId)
            ->create($request->validate(['content' => 'required|string']));

        return back()->with('success', 'Note added successfully.');
    }

    public function update(Request $request, Note $note)
    {
        $note->update($request->validate(['content' => 'required|string']));

        return back()->with('success', 'Note updated successfully.');
    }

    public function destroy(Note $note)
    {
        $note->delete();

        return back()->with('success', 'Note deleted successfully.');
    }
}
