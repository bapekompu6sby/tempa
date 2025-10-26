<?php

namespace Database\Seeders;

use App\Models\Instruction;
use Illuminate\Database\Seeder;

class InstructionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/instruction_seed.csv');
        if (!file_exists($path)) {
            $this->command->error("Instruction CSV not found at: {$path}");
            return;
        }

        $handle = fopen($path, 'r');
        if ($handle === false) {
            $this->command->error("Unable to open Instruction CSV: {$path}");
            return;
        }

        $headers = [];
        $rowNum = 0;
        while (($row = fgetcsv($handle)) !== false) {
            $rowNum++;
            // skip empty lines
            if ($rowNum === 1) {
                $headers = array_map(function($h){ return trim($h); }, $row);
                continue;
            }
            if (count($row) === 0) continue;

            // map row to associative array using headers
            $data = [];
            foreach ($headers as $i => $key) {
                $data[$key] = isset($row[$i]) ? trim($row[$i]) : null;
            }

            // normalize values
            $name = $data['name'] ?? null;
            if (empty($name)) continue;
            $role = $data['role'] ?? null;
            $detail = $data['detail'] ?? null;

            $linkable = isset($data['linkable']) && $data['linkable'] !== '' ? (bool) intval($data['linkable']) : false;
            $link_label = $data['link_label'] ?? null;
            $phase = $data['phase'] ?? 'pelaksanaan';

            $full_elearning = isset($data['full_elearning']) && $data['full_elearning'] !== '' ? (bool) intval($data['full_elearning']) : false;
            $distance_learning = isset($data['distance_learning']) && $data['distance_learning'] !== '' ? (bool) intval($data['distance_learning']) : false;
            $blended_learning = isset($data['blended_learning']) && $data['blended_learning'] !== '' ? (bool) intval($data['blended_learning']) : false;
            $classical = isset($data['classical']) && $data['classical'] !== '' ? (bool) intval($data['classical']) : false;

            Instruction::updateOrCreate(
                ['name' => $name, 'role' => $role],
                [
                    'detail' => $detail,
                    'linkable' => $linkable,
                    'link_label' => $link_label,
                    'phase' => $phase,
                    'full_elearning' => $full_elearning,
                    'distance_learning' => $distance_learning,
                    'blended_learning' => $blended_learning,
                    'classical' => $classical,
                ]
            );
        }

        fclose($handle);

        $this->command->info('Instruction CSV import complete.');
    }
}
