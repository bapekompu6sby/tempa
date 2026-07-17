<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MonitoringTemplate;

class BaseMonitoringTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['name' => 'Kehadiran Peserta (mengikuti proses pembelajaran dari awal sampai akhir pada setiap sesi)', 
                'category' => 'sikap', 
                'sub_category' => 'Kehadiran dan Ketepatan Sesi Pembelajaran', 
                'ownership' => 'asn_event_subject',
                'order' => 1],
            ['name' => 'Ketepatan Waktu (masuk room virtual sesuai waktu yang ditentukan / tidak terlambat)', 
                'category' => 'sikap', 
                'sub_category' => 'Kehadiran dan Ketepatan Sesi Pembelajaran', 
                'ownership' => 'asn_event_subject',
                'order' => 2],
            ['name' => 'Peserta berpakaian rapi dan sopan sesuai dengan ketentuan / tata tertib yang berlaku)', 
                'category' => 'sikap', 
                'sub_category' => 'Menjaga Etika dan Kesopanan', 
                'ownership' => 'asn_event_subject',
                'order' => 3],
            ['name' => 'Bersikap sopan terhadap Pengajar, SDM Penyelenggara, dan Peserta Pelatihan', 
                'category' => 'sikap', 
                'sub_category' => 'Menjaga Etika dan Kesopanan', 
                'ownership' => 'asn_event_subject',
                'order' => 4],
            ['name' => 'Peserta menggunakan latar belakang (virtual background) yang disediakan Penyelenggara', 
                'category' => 'sikap', 
                'sub_category' => 'Kepatuhan Tata Tertib', 
                'ownership' => 'asn_event_subject',
                'order' => 4],
            ['name' => 'Peserta login dengan menggunakan format : No. Urut_Nama Lengkap_Asal Instansi / Unor', 
                'category' => 'sikap', 
                'sub_category' => 'Kepatuhan Tata Tertib', 
                'ownership' => 'asn_event_subject',
                'order' => 5],
            ['name' => 'Peserta mematikan audio (audio mute) secara menerus pada saat Pengajar sedang menyampaikan materi', 
                'category' => 'sikap', 
                'sub_category' => 'Kepatuhan Tata Tertib', 
                'ownership' => 'asn_event_subject',
                'order' => 6],
            ['name' => 'Selama proses pembelajaran, Peserta secara terus menerus mengaktifkan kamera / video (video on)', 
                'category' => 'sikap', 
                'sub_category' => 'Kepatuhan Tata Tertib', 
                'ownership' => 'asn_event_subject',
                'order' => 7],
            ['name' => 'Selama proses pembelajaran, Peserta berada di tempat / lokasi yang statis dan representatif', 
                'category' => 'sikap', 
                'sub_category' => 'Kepatuhan Tata Tertib', 
                'ownership' => 'asn_event_subject',
                'order' => 8],
            ['name' => 'Peserta tidak meninggalkan ruang kelas virtual, kecuali mendesak (mengalami permasalahan teknis : koneksi internet mati, audio / video tidak berfungsi, dsb)', 
                'category' => 'sikap', 
                'sub_category' => 'Kepatuhan Tata Tertib', 
                'ownership' => 'asn_event_subject',
                'order' => 9],
            
        ];

        foreach ($data as $item) {
            MonitoringTemplate::create($item);
        }
    }
}
