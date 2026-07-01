<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\AsnImport;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AsnImportController extends Controller
{
    public function importForEvent(Request $request, \App\Models\Event $event)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // max 10MB
        ]);

        try {
            Excel::import(new \App\Imports\EventAsnImport($event->id), $request->file('file'));
            return back()->with('success', 'Data ASN berhasil diimport dan ditambahkan ke pelatihan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }
    public function showImportForm()
    {
        return view('asn.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240' // max 10MB
        ]);

        try {
            Excel::import(new AsnImport, $request->file('file'));
            return back()->with('success', 'Data ASN berhasil diimport!');
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris " . $failure->row() . ": " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Validasi gagal:<br>' . implode('<br>', $errorMessages));
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set Headers
        $headers = [
            'nip', 'name', 'job_title', 'phone_number', 'email',
            'birth_city', 'birth_date', 'gender', 'rank_grade',
            'latest_education', 'office_address', 'asn_type', 'asn_source'
        ];

        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Example row
        $example = [
            '199001012020121001', 'Budi Santoso', 'Analis Kebijakan', '08123456789', 'budi@example.com',
            'Jakarta', '1990-01-01', 'L', 'III/a',
            'S1', 'Jl. Merdeka No 1', 'pns', 'pusat'
        ];

        $col = 'A';
        foreach ($example as $data) {
            $sheet->setCellValue($col . '2', $data);
            $col++;
        }

        $writer = new Xlsx($spreadsheet);

        $response = new StreamedResponse(function() use ($writer) {
            $writer->save('php://output');
        });

        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment;filename="Template_Import_ASN.xlsx"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }
}
