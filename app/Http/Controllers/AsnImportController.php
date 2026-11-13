<?php

namespace App\Http\Controllers;

use App\Imports\AsnImport;
use App\Models\Event;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AsnImportController extends Controller
{
    public function showImportForm(Request $request)
    {
        $events = Event::orderBy('start_date', 'desc')->orderBy('name')->get();
        $selectedEventId = $request->query('event_id');

        return view('asn.import', compact('events', 'selectedEventId'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
            'event_id' => 'nullable|exists:events,id',
        ]);

        try {
            $eventId = $request->input('event_id');
            $importer = new AsnImport($eventId);
            Excel::import($importer, $request->file('file'));

            $imported = $importer->getImportedCount();
            $attached = $importer->getAttachedCount();

            $msg = "Berhasil mengimport {$imported} data ASN.";
            if ($attached > 0) {
                $event = $eventId ? Event::find($eventId) : null;
                $eventName = $event ? " pada pelatihan \"{$event->name}\"" : '';
                $msg .= " {$attached} peserta telah dihubungkan ke pelatihan{$eventName}.";
            }

            return back()->with('success', $msg);
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = 'Baris ' . $failure->row() . ': ' . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Validasi gagal:<br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function importForEvent(Request $request, Event $event)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv,txt|max:10240',
        ]);

        try {
            $importer = new AsnImport($event->id);
            Excel::import($importer, $request->file('file'));

            $imported = $importer->getImportedCount();
            return back()->with('success', "Berhasil mengimport {$imported} data ASN dan menambahkannya ke pelatihan!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Plain data headers without styling (treated as CSV-like table)
        $headers = [
            'nip', 'name', 'job_title', 'phone_number', 'email',
            'birth_city', 'birth_date', 'gender', 'rank_grade',
            'latest_education', 'office_address', 'asn_type', 'asn_source', 'event_id',
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValueExplicit($col . '1', $header, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;
        }

        // Example plain data row
        $example = [
            '199001012020121001', 'Budi Santoso', 'Analis Kebijakan', '08123456789', 'budi@example.com',
            'Jakarta', '1990-01-01', 'L', 'III/a',
            'S1', 'Jl. Merdeka No 1', 'pns', 'pusat', '',
        ];

        $col = 'A';
        foreach ($example as $data) {
            $sheet->setCellValueExplicit($col . '2', $data, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $col++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="Template_Import_ASN.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
