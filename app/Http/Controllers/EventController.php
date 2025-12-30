<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventInstruction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    $events = Event::all();
    return view('events.index', compact('events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
    return view('events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'learning_model' => 'nullable|in:full_elearning,distance_learning,blended_learning,classical',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'note' => 'nullable|string',
        ]);
        $event = Event::create($validated);
        return redirect()->route('events.index')->with('success', 'Pelatihan berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
    // Prepare tabbed instruction lists scoped to this event, with optional search
    $tab = request('tab', 'semua');
    $q = request('q');
    // phase filter: 'all' means no filtering
    $phase = request('phase', 'all');

    // load related instructions
    $event->load('eventInstructions.instruction');

    $build = function ($role = null) use ($event, $q, $phase) {
        $query = EventInstruction::with('instruction')->where('event_id', $event->id);
        if ($role) {
            $query->whereHas('instruction', function ($qi) use ($role) {
                $qi->where('role', $role);
            });
        }
        // apply phase filter when a specific phase is selected
        if (!empty($phase) && $phase !== 'all') {
            $query->whereHas('instruction', function ($qi) use ($phase) {
                $qi->where('phase', $phase);
            });
        }
        if ($q) {
            $query->whereHas('instruction', function ($qi) use ($q) {
                $qi->where('name', 'like', "%{$q}%")
                    ->orWhere('detail', 'like', "%{$q}%");
            });
        }

        // enforce specific phase ordering: persiapan -> pembukaan_pelatihan -> pelaksanaan -> penutupan_pelatihan -> evaluasi_pelatihan -> pasca_pelatihan
        $orderSql = "CASE phase
            WHEN 'persiapan' THEN 1
            WHEN 'pembukaan_pelatihan' THEN 2
            WHEN 'pelaksanaan' THEN 3
            WHEN 'penutupan_pelatihan' THEN 4
            WHEN 'evaluasi_pelatihan' THEN 5
            WHEN 'pasca_pelatihan' THEN 6
            ELSE 7 END";

        $query->orderByRaw($orderSql)->orderBy('id');

        // paginate results to 20 per page
        return $query->paginate(20);
    };

    $all = $build();
    $pic = $build('pic');
    $host = $build('host');
    $pengamat = $build('petugas_kelas');

    return view('events.show', compact('event', 'all', 'pic', 'host', 'pengamat', 'tab', 'q', 'phase'));
    }

    /**
     * Display documents (kelengkapan dokumen) for the specified event.
     */
    public function documents(Event $event)
    {
        $event->load('eventDocuments');

        $documents = $event->eventDocuments()->get();

        return view('events.documents', compact('event', 'documents'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
    return view('events.edit', compact('event'));
    }

        /**
     * Download the event report file.
     */
    public function downloadReport(Event $event)
    {
        if (!$event->event_report_url || !\Storage::disk('public')->exists($event->event_report_url)) {
            abort(404, 'File laporan tidak ditemukan.');
        }
        $filename = basename($event->event_report_url);
        return response()->download(storage_path('app/public/' . $event->event_report_url), $filename);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'learning_model' => 'nullable|in:full_elearning,distance_learning,blended_learning,classical',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'note' => 'nullable|string',
            'document_drive_url' => 'nullable|url',
            'event_report_file' => 'nullable|file|mimes:pdf,doc,docx',
        ]);

        // Handle file upload if present
        if ($request->hasFile('event_report_file')) {
            $file = $request->file('event_report_file');
            $path = $file->store('event_reports', 'public');
            $validated['event_report_url'] = $path;
        }

        $event->update($validated);
        return redirect()->route('events.index')->with('success', 'Pelatihan berhasil diupdate');
    }

    /**
     * Mark event as selesai.
     */
    public function finish(Event $event)
    {
        $event->update(['status' => 'selesai']);
        return redirect()->route('events.show', $event)->with('success', 'Pelatihan telah ditandai sebagai selesai.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
    $event->delete();
    return redirect()->route('events.index')->with('success', 'Pelatihan berhasil dihapus');
    }
}
