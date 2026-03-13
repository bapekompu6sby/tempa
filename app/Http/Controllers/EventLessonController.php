<?php

namespace App\Http\Controllers;

use App\Models\EventLesson;
use App\Http\Requests\StoreEventLessonRequest;
use App\Http\Requests\UpdateEventLessonRequest;

class EventLessonController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $lessons = $event->eventLessons()->get();
        return view('event_lessons.index', compact('event', 'lessons'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create($eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        return view('event_lessons.create', compact('event'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEventLessonRequest $request, $eventId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $lesson = $event->eventLessons()->create($request->only(['title', 'description']));
        return redirect()->route('events.lessons.index', ['event' => $event->id])->with('success', 'Mata pelatihan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show($eventId, $lessonId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $lesson = $event->eventLessons()->findOrFail($lessonId);
        return view('event_lessons.show', compact('event', 'lesson'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($eventId, $lessonId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $lesson = $event->eventLessons()->findOrFail($lessonId);
        return view('event_lessons.edit', compact('event', 'lesson'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEventLessonRequest $request, $eventId, $lessonId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $lesson = $event->eventLessons()->findOrFail($lessonId);
        $lesson->update($request->only(['title', 'description']));
        return redirect()->route('events.lessons.index', ['event' => $event->id])->with('success', 'Mata pelatihan berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($eventId, $lessonId)
    {
        $event = \App\Models\Event::findOrFail($eventId);
        $lesson = $event->eventLessons()->findOrFail($lessonId);
        $lesson->delete();
        return redirect()->route('events.lessons.index', ['event' => $event->id])->with('success', 'Mata pelatihan berhasil dihapus.');
    }
}
