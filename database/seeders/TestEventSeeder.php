<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Asn;
use App\Models\EventSubject;

class TestEventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an Event
        $event = Event::create([
            'name' => 'Test Pelatihan Item',
            'learning_model' => 'classical',
            'start_date' => '2026-07-10',
            'end_date' => '2026-07-15',
            'target' => 30,
            'jp_module' => 20,
            'jp_facilitator' => 20,
            'field' => 'teknis',
            'status' => 'belum_dimulai'
        ]);

        // Create 3 ASNs
        $asns = [];
        for ($i = 1; $i <= 3; $i++) {
            $asns[] = Asn::firstOrCreate(
                ['nip' => '19900101201001100' . $i],
                [
                    'name' => 'Participant ' . $i,
                    'email' => 'participant' . $i . '@example.com',
                    'job_title' => 'Staff',
                    'phone_number' => '08123456789' . $i,
                    'birth_city' => 'Jakarta',
                    'birth_date' => '1990-01-01',
                    'gender' => 'L',
                    'rank_grade' => 'III/a',
                    'latest_education' => 'S1',
                    'office_address' => 'Jl. Test No. ' . $i,
                    'asn_type' => 'PNS',
                    'asn_source' => 'pusat',
                ]
            );
        }

        // Attach ASNs to Event as participants
        foreach ($asns as $asn) {
            $event->asns()->attach($asn->id, [
                'passing_status' => 'lulus',
                'participant_type' => 'utama',
                'participant_status' => 'terdaftar',
            ]);
        }

        // Create Event Subjects
        EventSubject::create([
            'event_id' => $event->id,
            'name' => 'Materi Dasar 1',
            'jp' => 2
        ]);
        
        EventSubject::create([
            'event_id' => $event->id,
            'name' => 'Materi Inti 1',
            'jp' => 4
        ]);
    }
}
