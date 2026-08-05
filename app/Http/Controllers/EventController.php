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
    public function index(Request $request)
    {
        // Filter by year and month
        $year = $request->input('year');
        $month = $request->input('month');
        $query = Event::query();
        if (in_array($year, ['2025', '2026'])) {
            $query->whereYear('start_date', $year);
        }
        if (!empty($month)) {
            $query->whereMonth('start_date', $month);
        }
        // Search by event name
        $q = $request->input('q');
        if (!empty($q)) {
            $query->where('name', 'like', "%{$q}%");
        }
        // Sort by start_date descending
        $query->orderBy('start_date', 'desc');
        // Paginate (10 per page)
        $events = $query->paginate(10)->appends($request->except('page'));

        // For filter dropdown
        $years = ['2025', '2026'];

        // Pass year, month, years to view
        return view('events.index', compact('events', 'year', 'month', 'years'));
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
        // Get main tab: instruksi, peserta, mata_pelatihan
        $main_tab = request('main_tab', 'peserta');

        // Prepare tabbed instruction lists scoped to this event, with optional search
        $tab = request('tab', 'semua');
        $q = request('q');
        // phase filter: 'all' means no filtering
        $phase = request('phase', 'all');

        $all = collect();
        $pic = collect();
        $host = collect();
        $pengamat = collect();
        $participants = null;
        $subjects = null;

        if ($main_tab === 'instruksi') {
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
                return $query->paginate(20)->appends(request()->except('page'));
            };

            $all = $build();
            $pic = $build('pic');
            $host = $build('host');
            $pengamat = $build('petugas_kelas');
        } elseif ($main_tab === 'peserta') {
            $participants = $event->asns()->paginate(20)->appends(request()->except('page'));
        } elseif ($main_tab === 'mata_pelatihan') {
            $subjects = $event->eventSubjects()->get();
        }

        return view('events.show', compact('event', 'all', 'pic', 'host', 'pengamat', 'tab', 'q', 'phase', 'main_tab', 'participants', 'subjects'));
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
     * Display participants for the specified event.
     */
    public function participants(Event $event)
    {
        $participants = $event->asns()->paginate(20);
        return view('events.participants', compact('event', 'participants'));
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
            'event_report_file' => 'nullable|file|mimes:pdf,doc,docx,zip,rar',
            'target' => 'nullable|integer',
            'jp_module' => 'nullable|integer',
            'jp_facilitator' => 'nullable|integer',
            'field' => 'nullable|string|max:255',
            'status' => 'nullable|in:tentative,belum_dimulai,persiapan,pelaksanaan,pelaporan,dibatalkan,selesai',
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
     * Generate asn_event_subject records for the event.
     */
    public function generateAsnEventSubjects(Event $event)
    {
        $event->generateAsnEventSubjects();
        return back()->with('success', 'ASN Event Subjects berhasil digenerate.');
    }

    /**
     * Generate monitoring items for the event.
     */
    public function generateMonitoringItems(Event $event)
    {
        $event->generateMonitoringItems();
        return back()->with('success', 'Monitoring Items berhasil digenerate.');
    }

    /**
     * Calculate behavioral scores for the event.
     */
    public function calculateBehavioralScores(Event $event)
    {
        $event->calculateBehavioralScores();
        return back()->with('success', 'Behavioral scores berhasil dihitung.');
    }

    /**
     * View subject monitoring for the event subject.
     */
    public function subjectMonitoring(Event $event, \App\Models\EventSubject $subject)
    {
        $asnEventSubjects = \Illuminate\Support\Facades\DB::table('asn_event_subject')
            ->join('asn_event', 'asn_event_subject.asn_event_id', '=', 'asn_event.id')
            ->join('asns', 'asn_event.asn_id', '=', 'asns.id')
            ->where('asn_event.event_id', $event->id)
            ->where('asn_event_subject.event_subject_id', $subject->id)
            ->select('asn_event_subject.id as aes_id', 'asns.name', 'asns.nip', 'asn_event_subject.behavioral_score')
            ->get();

        $monitoringItems = \App\Models\MonitoringItem::with('template')
            ->where('monitorable_type', 'asn_event_subject')
            ->whereIn('monitorable_id', $asnEventSubjects->pluck('aes_id'))
            ->orderBy('id')
            ->get()
            ->groupBy('monitorable_id');

        return view('events.subject_monitoring', compact('event', 'subject', 'asnEventSubjects', 'monitoringItems'));
    }

    /**
     * Save subject monitoring progress.
     */
    public function saveSubjectMonitoring(Request $request, Event $event, \App\Models\EventSubject $subject)
    {
        $allItems = $request->input('all_item_ids', []);
        $checkedItems = $request->input('items', []);

        foreach ($allItems as $id) {
            $val = isset($checkedItems[$id]) ? 1 : 0;
            \App\Models\MonitoringItem::where('id', $id)->update(['value' => $val]);
        }

        return back()->with('success', 'Progress monitoring sikap berhasil disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
    $event->delete();
    return redirect()->route('events.index')->with('success', 'Pelatihan berhasil dihapus');
    }
    /**
     * Display yearly calendar of events grouped by month.
     */
    public function kalender(Request $request)
    {
        $years = range(2025, date('Y') + 1); // Example: 5 years back, 2 years forward
        $year = $request->input('year', now()->year);
        $events = Event::whereYear('start_date', $year)
            ->orderBy('start_date', 'asc')
            ->select(['id', 'name', 'start_date', 'end_date', 'learning_model', 'status', 'note', 'target', 'jp_module', 'jp_facilitator', 'field'])
            ->get();

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Ags', 'Sep', 'Okt', 'Nov', 'Des'];
        $allMonths = $monthNames;

        return view('events.kalender', compact('events', 'allMonths', 'year', 'years'));
    }
}
