<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ImportEvents2025Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $csvPath = database_path('seeders/import_event_2025.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("Unable to open CSV file: {$csvPath}");
            return;
        }

        $header = null;
        $rowCount = 0;
        $created = 0;
        $updated = 0;

        DB::beginTransaction();
        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);
                if ($line === '') continue;

                $cols = str_getcsv($line, ';');

                // header detection (first line)
                if (!$header) {
                    $header = array_map('trim', $cols);
                    // normalize header to lower keys
                    $header = array_map(function ($h) { return strtolower($h); }, $header);
                    continue;
                }

                $data = array_combine($header, $cols);
                if (!$data) continue;

                $name = trim($data['name'] ?? '');
                if ($name === '') continue;

                $learning_model = trim($data['learning_model'] ?? null) ?: null;

                $start_date = trim($data['start_date'] ?? '') ?: null;
                $end_date = trim($data['end_date'] ?? '') ?: null;
                $status = trim($data['status'] ?? '') ?: null;

                // Normalize dates to null or Y-m-d
                try {
                    $sd = $start_date ? Carbon::parse($start_date)->toDateString() : null;
                } catch (\Exception $e) {
                    $sd = null;
                }
                try {
                    $ed = $end_date ? Carbon::parse($end_date)->toDateString() : null;
                } catch (\Exception $e) {
                    $ed = null;
                }

                $attributes = [
                    'name' => $name,
                    'learning_model' => $learning_model,
                    'start_date' => $sd,
                    'end_date' => $ed,
                    'status' => $status,
                ];

                // Avoid exact duplicates by name + start_date
                $existing = Event::where('name', $name)
                    ->where(function ($q) use ($sd) {
                        if (is_null($sd)) {
                            $q->whereNull('start_date');
                        } else {
                            $q->where('start_date', $sd);
                        }
                    })->first();

                if ($existing) {
                    $existing->update(array_filter($attributes, fn($v) => !is_null($v)));
                    $updated++;
                    $this->command->info("Updated event: {$name} ({$sd})");
                } else {
                    Event::create($attributes);
                    $created++;
                    $this->command->info("Created event: {$name} ({$sd})");
                }

                $rowCount++;
            }

            fclose($handle);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            $this->command->error('Import failed: ' . $e->getMessage());
            return;
        }

        $this->command->info("Import finished. Rows processed: {$rowCount}. Created: {$created}. Updated: {$updated}.");
    }
}
