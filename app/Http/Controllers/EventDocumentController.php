<?php

namespace App\Http\Controllers;

use App\Models\EventDocument;
use App\Models\EventDocumentFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

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
        $downloadUrl = $eventDocument->file_path ? route('documents.download', $eventDocument) : null;

        return response()->json(['success' => true, 'eventDocument' => $eventDocument, 'url' => $url, 'download_url' => $downloadUrl]);
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
            'file' => 'required|file|max:122880', // max 10MB
        ]);

        $file = $request->file('file');
        if (!$file) {
            return response()->json(['success' => false, 'message' => 'No file uploaded'], 422);
        }

        $path = $file->store('event_documents', 'public');

        $eventDocument->file_path = $path;
        $eventDocument->save();

        $url = Storage::url($path);
        $downloadUrl = route('documents.download', $eventDocument);

        return response()->json(['success' => true, 'file_path' => $path, 'url' => $url, 'download_url' => $downloadUrl, 'eventDocument' => $eventDocument]);
    }

    /**
     * Upload multiple files for an EventDocument (files[] in form data).
     */
    public function uploadMultiple(Request $request, EventDocument $eventDocument)
    {
        $validated = $request->validate([
            'files.*' => 'required|file|max:122880',
        ]);

        $saved = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('event_documents', 'public');
                $record = EventDocumentFile::create([
                    'event_document_id' => $eventDocument->id,
                    'original_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'mime' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                ]);
                $saved[] = $record;
            }
        }

        return response()->json(['success' => true, 'files' => $saved]);
    }

    /**
     * Delete a single attachment record and its file.
     */
    public function destroyFile(EventDocumentFile $file)
    {
        // authorization can be added here
        Storage::disk('public')->delete($file->file_path);
        $file->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Download a single attachment file.
     */
    public function downloadFile(EventDocumentFile $file)
    {
        if (!Storage::disk('public')->exists($file->file_path)) {
            abort(404);
        }

        return Storage::disk('public')->download($file->file_path, $file->original_name);
    }

    /**
     * Download all attachments for an EventDocument as a zip.
     */
    public function downloadZip(EventDocument $eventDocument)
    {
        $files = $eventDocument->files()->get();
        if ($files->isEmpty()) {
            abort(404);
        }

        $zipName = Str::slug($eventDocument->name ?: 'documents') . '.zip';
        $tempFile = tempnam(sys_get_temp_dir(), 'zip');

        $zip = new ZipArchive();
        if ($zip->open($tempFile, ZipArchive::CREATE) !== true) {
            abort(500, 'Could not create zip archive.');
        }

        foreach ($files as $f) {
            $fullPath = storage_path('app/public/' . $f->file_path);
            if (file_exists($fullPath)) {
                $insideName = $f->original_name;
                $zip->addFile($fullPath, $insideName);
            }
        }

        $zip->close();

        return response()->download($tempFile, $zipName)->deleteFileAfterSend(true);
    }

    /**
     * Stream a file download with a suggested filename: "{EventDocument name} - {Event name}.{ext}"
     */
    public function download(EventDocument $eventDocument)
    {
        if (empty($eventDocument->file_path) || !\Illuminate\Support\Facades\Storage::disk('public')->exists($eventDocument->file_path)) {
            abort(404);
        }

    // build a friendly filename and preserve extension
    // replace both forward and back slashes with hyphens to avoid regex escaping issues
    $safeName = str_replace(['/', '\\'], '-', $eventDocument->name ?? 'document');
    $eventName = str_replace(['/', '\\'], '-', optional($eventDocument->event)->name ?? 'event');
        $ext = pathinfo($eventDocument->file_path, PATHINFO_EXTENSION);
        $downloadName = trim($safeName);
        if ($eventName) {
            $downloadName .= ' - ' . trim($eventName);
        }
        if ($ext) {
            $downloadName .= '.' . $ext;
        }

        return Storage::disk('public')->download($eventDocument->file_path, $downloadName);
    }
}
