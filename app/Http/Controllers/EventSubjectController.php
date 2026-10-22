<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventSubject;
use App\Models\MonitoringItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EventSubjectController extends Controller
{
    /**
     * Display listing of subjects for management.
     */
    public function index(Event $event)
    {
        $subjects = $event->eventSubjects()->get();
        return view('events.subjects', compact('event', 'subjects'));
    }

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

        $aesIds = DB::table('asn_event_subject')
            ->where('event_subject_id', $subject->id)
            ->pluck('id');

        if ($aesIds->isNotEmpty()) {
            MonitoringItem::where('monitorable_type', 'asn_event_subject')
                ->whereIn('monitorable_id', $aesIds)
                ->delete();
        }

        $subject->delete();

        return back()->with('success', 'Mata Pelatihan berhasil dihapus.');
    }
}
