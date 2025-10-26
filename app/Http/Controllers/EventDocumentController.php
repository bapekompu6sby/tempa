<?php

namespace App\Http\Controllers;

use App\Models\EventDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(EventDocument $eventDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventDocument $eventDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventDocument $eventDocument)
    {
        $data = $request->validate([
            'link' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        // normalize link to include scheme if missing
        if (!empty($data['link']) && !preg_match('/^[a-zA-Z][a-zA-Z0-9+.-]*:\/\//', $data['link'])) {
            $data['link'] = 'https://' . ltrim($data['link'], '/');
        }

        $eventDocument->fill($data);
        $eventDocument->save();

        $url = $eventDocument->file_path ? Storage::url($eventDocument->file_path) : null;

        return response()->json(['success' => true, 'eventDocument' => $eventDocument, 'url' => $url]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventDocument $eventDocument)
    {
        //
    }

    /**
     * Upload a file for the EventDocument and save its storage path.
     */
    public function upload(Request $request, EventDocument $eventDocument)
    {
        $validated = $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('file');
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 422);
        }

        $path = $file->store('event_documents', 'public');

        $eventDocument->file_path = $path;
        $eventDocument->save();

        $url = Storage::url($path);

        return response()->json(['success' => true, 'file_path' => $path, 'url' => $url, 'eventDocument' => $eventDocument]);
    }
}
