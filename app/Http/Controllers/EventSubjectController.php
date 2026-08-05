<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSubject;
use Illuminate\Http\Request;

class EventSubjectController extends Controller
{
    public function store(Request $request, Event $event)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'jp' => 'required|integer|min:1',
        ]);

        $event->eventSubjects()->create($request->only('name', 'jp'));

        return back()->with('success', 'Mata Pelatihan berhasil ditambahkan.');
    }

    public function update(Request $request, Event $event, EventSubject $subject)
    {
        // Ensure the subject belongs to the event
        if ($subject->event_id !== $event->id) {
            abort(404);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'jp' => 'required|integer|min:1',
        ]);

        $subject->update($request->only('name', 'jp'));

        return back()->with('success', 'Mata Pelatihan berhasil diperbarui.');
    }

    public function destroy(Event $event, EventSubject $subject)
    {
        // Ensure the subject belongs to the event
        if ($subject->event_id !== $event->id) {
            abort(404);
        }

        $subject->delete();

        return back()->with('success', 'Mata Pelatihan berhasil dihapus.');
    }
}
