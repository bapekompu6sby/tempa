<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MonitoringTemplate;

class MonitoringTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Kedisiplinan Kehadiran', 'category' => 'sikap', 'sub_category' => 'Kehadiran', 'ownership' => 'asn_event_subject'],
            ['name' => 'Sikap dan Perilaku', 'category' => 'sikap', 'sub_category' => 'Perilaku', 'ownership' => 'asn_event_subject'],
            ['name' => 'Kerjasama Tim', 'category' => 'sikap', 'sub_category' => 'Kerjasama', 'ownership' => 'asn_event_subject'],
            ['name' => 'Tanggung Jawab', 'category' => 'sikap', 'sub_category' => 'Tanggung Jawab', 'ownership' => 'asn_event_subject'],
            ['name' => 'Ketersediaan Ruangan', 'category' => 'sarpras', 'sub_category' => 'Ruangan', 'ownership' => 'event'],
            ['name' => 'Kesiapan Peralatan', 'category' => 'sarpras', 'sub_category' => 'Peralatan', 'ownership' => 'event'],
            ['name' => 'Kelengkapan Dokumen', 'category' => 'administrasi', 'sub_category' => 'Dokumen', 'ownership' => 'event'],
            ['name' => 'Laporan Kegiatan', 'category' => 'administrasi', 'sub_category' => 'Laporan', 'ownership' => 'event'],
            ['name' => 'Biodata Peserta', 'category' => 'administrasi_peserta', 'sub_category' => 'Biodata', 'ownership' => 'asn_event'],
            ['name' => 'Sertifikat Peserta', 'category' => 'administrasi_peserta', 'sub_category' => 'Sertifikat', 'ownership' => 'asn_event'],
        ];

        foreach ($data as $item) {
            MonitoringTemplate::create($item);
        }
    }
}
