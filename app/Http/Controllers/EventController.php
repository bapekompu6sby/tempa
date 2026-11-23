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
        // Get main tab: instruksi, peserta, mata_pelatihan, monitoring
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
        $monitoringSetup = null;

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
        } elseif ($main_tab === 'monitoring') {
            $monitoringSetup = $event->getMonitoringSetupStatus();
        }

        return view('events.show', compact('event', 'all', 'pic', 'host', 'pengamat', 'tab', 'q', 'phase', 'main_tab', 'participants', 'subjects', 'monitoringSetup'));
    }

    /**
     * Display documents (kelengkapan dokumen) for the specified event.
     */
    public function documents(Event $event)
    {
        $documents = $event->eventDocuments()
            ->with('files')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->get();

        return view('events.documents', compact('event', 'documents'));
    }

    /**
     * Display participants for the specified event (Manajemen Peserta).
     */
    public function participants(Request $request, Event $event)
    {
        $q = $request->input('q');
        $query = $event->asns();
        if (!empty($q)) {
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('nip', 'like', "%{$q}%");
            });
        }
        $participants = $query->paginate(50)->appends($request->except('page'));
        $events = Event::orderBy('start_date', 'desc')->orderBy('name')->get();
        return view('events.participants', compact('event', 'participants', 'q', 'events'));
    }

    /**
     * Remove participant from event.
     */
    public function destroyParticipant(Event $event, \App\Models\Asn $asn)
    {
        $asnEvent = \Illuminate\Support\Facades\DB::table('asn_event')
            ->where('event_id', $event->id)
            ->where('asn_id', $asn->id)
            ->first();

        if ($asnEvent) {
            $aesIds = \Illuminate\Support\Facades\DB::table('asn_event_subject')
                ->where('asn_event_id', $asnEvent->id)
                ->pluck('id');

            if ($aesIds->isNotEmpty()) {
                \App\Models\MonitoringItem::where('monitorable_type', 'asn_event_subject')
                    ->whereIn('monitorable_id', $aesIds)
                    ->delete();
            }

            \App\Models\MonitoringItem::where('monitorable_type', 'asn_event')
                ->where('monitorable_id', $asnEvent->id)
                ->delete();

            $event->asns()->detach($asn->id);
        }

        return back()->with('success', 'Peserta berhasil dihapus dari pelatihan.');
    }

    /**
     * Display subjects for the specified event (Manajemen Mata Pelatihan).
     */
    public function subjects(Event $event)
    {
        $subjects = $event->eventSubjects()->get();
        return view('events.subjects', compact('event', 'subjects'));
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
        return back()->with('success', 'Participant Event Subjects berhasil digenerate.');
    }

    /**
     * Generate monitoring items for the event.
     */
    public function generateMonitoringItems(Request $request, Event $event)
    {
        $category = $request->input('category');
        $event->generateMonitoringItems($category);

        $catLabel = $category ? ' kategori ' . ucwords(str_replace('_', ' ', $category)) : '';
        return back()->with('success', "Monitoring Items{$catLabel} berhasil digenerate.");
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
     * Export sikap perilaku monitoring to XLSX with one sheet per event subject.
     */
    public function exportSikapMonitoring(Event $event)
    {
        $subjects = $event->eventSubjects()->orderBy('id')->get();
        if ($subjects->isEmpty()) {
            return back()->with('error', 'Belum ada mata pelatihan untuk diexport.');
        }

        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $usedTitles = [];

        foreach ($subjects as $sheetIndex => $subject) {
            $sheet = $sheetIndex === 0
                ? $spreadsheet->getActiveSheet()
                : $spreadsheet->createSheet();

            // Build valid, unique Excel sheet title (max 31 chars, no \ / ? * [ ] :)
            $cleanName = preg_replace('/[\\\\\/\?\*\[\]\:]/', '', (string) $subject->name);
            $cleanName = trim($cleanName) ?: ('Mapel ' . ($sheetIndex + 1));
            $baseTitle = mb_substr($cleanName, 0, 31);
            $title = $baseTitle;
            $suffix = 2;
            while (in_array(mb_strtolower($title), $usedTitles, true)) {
                $suffixStr = ' (' . $suffix . ')';
                $title = mb_substr($cleanName, 0, 31 - mb_strlen($suffixStr)) . $suffixStr;
                $suffix++;
            }
            $usedTitles[] = mb_strtolower($title);
            $sheet->setTitle($title);

            $asnEventSubjects = \Illuminate\Support\Facades\DB::table('asn_event_subject')
                ->join('asn_event', 'asn_event_subject.asn_event_id', '=', 'asn_event.id')
                ->join('asns', 'asn_event.asn_id', '=', 'asns.id')
                ->where('asn_event.event_id', $event->id)
                ->where('asn_event_subject.event_subject_id', $subject->id)
                ->orderBy('asns.name')
                ->select('asn_event_subject.id as aes_id', 'asns.name', 'asns.nip', 'asn_event_subject.behavioral_score')
                ->get();

            $monitoringItems = \App\Models\MonitoringItem::with('template')
                ->where('monitorable_type', 'asn_event_subject')
                ->whereIn('monitorable_id', $asnEventSubjects->pluck('aes_id'))
                ->orderBy('order')
                ->orderBy('id')
                ->get()
                ->groupBy('monitorable_id');

            // Determine item columns from first participant with items
            $headerItems = collect();
            foreach ($asnEventSubjects as $aes) {
                $items = $monitoringItems->get($aes->aes_id, collect());
                if ($items->isNotEmpty()) {
                    $headerItems = $items;
                    break;
                }
            }

            // Title metadata
            $sheet->setCellValue('A1', 'Monitoring Sikap Perilaku: ' . $subject->name);
            $sheet->setCellValue('A2', 'Pelatihan: ' . $event->name . ' | JP: ' . ($subject->jp ?? '-'));
            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(12);
            $sheet->getStyle('A2')->getFont()->setSize(10);

            // Header row (Row 4)
            $headerRow = 4;
            $headers = ['No', 'NIP', 'Nama Peserta'];
            foreach ($headerItems as $item) {
                $headers[] = $item->name;
            }
            $headers[] = 'Nilai Sikap';

            foreach ($headers as $colIdx => $heading) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue($colLetter . $headerRow, $heading);
            }

            $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));
            $headerRange = 'A' . $headerRow . ':' . $lastColLetter . $headerRow;
            $sheet->getStyle($headerRange)->applyFromArray([
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E5E7EB'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                ],
            ]);

            // Data rows
            $rowNum = 5;
            foreach ($asnEventSubjects as $index => $aes) {
                $sheet->setCellValue('A' . $rowNum, $index + 1);
                $sheet->setCellValueExplicit('B' . $rowNum, (string) ($aes->nip ?? '-'), \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
                $sheet->setCellValue('C' . $rowNum, $aes->name);

                $items = $monitoringItems->get($aes->aes_id, collect());
                $colIdx = 4;
                if ($items->isNotEmpty()) {
                    foreach ($items as $item) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                        $sheet->setCellValue($colLetter . $rowNum, $item->value ? 1 : 0);
                        $sheet->getStyle($colLetter . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $colIdx++;
                    }
                } else {
                    foreach ($headerItems as $ignored) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                        $sheet->setCellValue($colLetter . $rowNum, '-');
                        $sheet->getStyle($colLetter . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                        $colIdx++;
                    }
                }

                $scoreColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx);
                $sheet->setCellValue($scoreColLetter . $rowNum, round((float) ($aes->behavioral_score ?? 0), 1));
                $sheet->getStyle($scoreColLetter . $rowNum)->getNumberFormat()->setFormatCode('0.0');
                $sheet->getStyle($scoreColLetter . $rowNum)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $rowRange = 'A' . $rowNum . ':' . $lastColLetter . $rowNum;
                $sheet->getStyle($rowRange)->applyFromArray([
                    'borders' => [
                        'allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN],
                    ],
                ]);

                $rowNum++;
            }

            // Set column widths
            $sheet->getColumnDimension('A')->setWidth(6);
            $sheet->getColumnDimension('B')->setWidth(22);
            $sheet->getColumnDimension('C')->setWidth(32);
            for ($c = 4; $c < count($headers); $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->getColumnDimension($colLetter)->setWidth(22);
            }
            $sheet->getColumnDimension($lastColLetter)->setWidth(14);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $fileName = 'Monitoring_Sikap_' . Str::slug($event->name, '_') . '.xlsx';

        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
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
