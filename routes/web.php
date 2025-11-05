<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // Load events and pre-compute instruction counts per phase to avoid N+1 queries in the view
    $events = \App\Models\Event::orderBy('start_date', 'desc')
        ->withCount([
            // persiapan
            'eventInstructions as persiapan_total' => function ($q) { $q->where('phase', 'persiapan'); },
            'eventInstructions as persiapan_checked' => function ($q) { $q->where('phase', 'persiapan')->where('checked', true); },
            // pelaksanaan
            'eventInstructions as pelaksanaan_total' => function ($q) { $q->where('phase', 'pelaksanaan'); },
            'eventInstructions as pelaksanaan_checked' => function ($q) { $q->where('phase', 'pelaksanaan')->where('checked', true); },
            // pelaporan
            'eventInstructions as pelaporan_total' => function ($q) { $q->where('phase', 'pelaporan'); },
            'eventInstructions as pelaporan_checked' => function ($q) { $q->where('phase', 'pelaporan')->where('checked', true); },
        ])
        ->get();

    return view('welcome', compact('events'));
});
Route::post('/unlock', function (\Illuminate\Http\Request $request) {
    $request->validate(['password' => 'required']);
    $expected = env('APP_UNLOCK_PASSWORD', 'tempa');

    if ($request->input('password') === $expected) {
        $request->session()->put('access_granted', true);
        return redirect()->to('/');
    }

    return back()->withErrors(['password' => 'Password is incorrect']);
});
// Public-facing event pages (no password required)
Route::get('public/events', function (\Illuminate\Http\Request $request) {
    // available years for filter dropdown (distinct years with start_date)
    $years = \App\Models\Event::selectRaw('YEAR(start_date) as year')
        ->whereNotNull('start_date')
        ->distinct()
        ->orderByDesc('year')
        ->pluck('year')
        ->filter()
        ->values();

    // Default to current year/month when the user did not provide filter parameters.
    // Respect an explicit empty selection ("Semua") by checking has().
    $year = $request->has('year') ? $request->query('year') : \Carbon\Carbon::now()->year;
    $month = $request->has('month') ? $request->query('month') : \Carbon\Carbon::now()->month;
    $status = $request->query('status');

    $eventsQuery = \App\Models\Event::orderBy('start_date', 'desc')
        ->withCount([
            'eventInstructions as persiapan_total' => function ($q) { $q->where('phase', 'persiapan'); },
            'eventInstructions as persiapan_checked' => function ($q) { $q->where('phase', 'persiapan')->where('checked', true); },
            'eventInstructions as pelaksanaan_total' => function ($q) { $q->where('phase', 'pelaksanaan'); },
            'eventInstructions as pelaksanaan_checked' => function ($q) { $q->where('phase', 'pelaksanaan')->where('checked', true); },
            'eventInstructions as pelaporan_total' => function ($q) { $q->where('phase', 'pelaporan'); },
            'eventInstructions as pelaporan_checked' => function ($q) { $q->where('phase', 'pelaporan')->where('checked', true); },
        ]);

    if ($year) {
        $eventsQuery->whereYear('start_date', $year);
    }
    if ($month) {
        $eventsQuery->whereMonth('start_date', $month);
    }
    if ($status) {
        $eventsQuery->where('status', $status);
    }

    $events = $eventsQuery->get();

    return view('events.public_index', compact('events', 'years', 'year', 'month', 'status'));
});
// (public event detail route removed — only public events index remains)

// Protect application routes behind the simple password middleware (welcome page stays public)
Route::middleware([\App\Http\Middleware\RequirePassword::class])->group(function () {
    Route::resource('instructions', App\Http\Controllers\InstructionController::class);
    Route::resource('events', App\Http\Controllers\EventController::class);
    // view event documents
    Route::get('events/{event}/documents', [App\Http\Controllers\EventController::class, 'documents'])->name('events.documents');
    // Use 'eventDocument' as the route parameter name so it matches controller method signatures
    Route::resource('documents', App\Http\Controllers\EventDocumentController::class)->parameters([
        'documents' => 'eventDocument'
    ]);
    // upload file for event document
    Route::post('documents/{eventDocument}/upload', [App\Http\Controllers\EventDocumentController::class, 'upload'])->name('documents.upload');
    // upload multiple files for an event document
    Route::post('documents/{eventDocument}/files', [App\Http\Controllers\EventDocumentController::class, 'uploadMultiple'])->name('documents.files.upload');
    // download or delete individual attachment files
    Route::get('documents/files/{file}/download', [App\Http\Controllers\EventDocumentController::class, 'downloadFile'])->name('documents.files.download');
    Route::delete('documents/files/{file}', [App\Http\Controllers\EventDocumentController::class, 'destroyFile'])->name('documents.files.destroy');
    // download all attachments as zip
    Route::get('documents/{eventDocument}/download-zip', [App\Http\Controllers\EventDocumentController::class, 'downloadZip'])->name('documents.files.downloadZip');
    // download (force-download) for event document
    Route::get('documents/{eventDocument}/download', [App\Http\Controllers\EventDocumentController::class, 'download'])->name('documents.download');
    // EventInstruction toggle route
    Route::patch('event-instructions/{eventInstruction}/toggle', [App\Http\Controllers\EventInstructionController::class, 'toggle'])->name('event-instructions.toggle');
    // EventInstruction update route (used for saving link and other inline updates)
    Route::patch('event-instructions/{eventInstruction}', [App\Http\Controllers\EventInstructionController::class, 'update'])->name('event-instructions.update');
});
