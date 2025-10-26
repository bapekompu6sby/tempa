<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $path = database_path('seeders/data/document_templates.csv');

        if (!file_exists($path)) {
            $this->command->error("CSV file not found: {$path}");
            return;
        }

        if (($handle = fopen($path, 'r')) === false) {
            $this->command->error("Unable to open CSV file: {$path}");
            return;
        }

        $header = fgetcsv($handle);
        if (!$header) {
            $this->command->error('CSV appears empty or invalid.');
            fclose($handle);
            return;
        }

        // normalize header
        $header = array_map(function ($h) { return trim($h); }, $header);

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) !== count($header)) {
                // skip malformed row
                continue;
            }

            $data = array_combine($header, $row);

            // coerce booleans
            $record = [
                'name' => trim($data['name'] ?? ''),
                'full_elearning' => $this->toBool($data['full_elearning'] ?? ''),
                'distance_learning' => $this->toBool($data['distance_learning'] ?? ''),
                'blended_learning' => $this->toBool($data['blended_learning'] ?? ''),
                'classical' => $this->toBool($data['classical'] ?? ''),
            ];

            if (empty($record['name'])) {
                continue;
            }

            // create or update by name to make seeder idempotent
            DocumentTemplate::updateOrCreate(
                ['name' => $record['name']],
                $record
            );
        }

        fclose($handle);

        $this->command->info('Document templates seeded from CSV.');
    }

    private function toBool($v)
    {
        $v = trim((string) $v);
        if ($v === '') return false;
        $low = strtolower($v);
        return in_array($low, ['1', 'true', 'yes', 'y', 'on'], true);
    }
}
