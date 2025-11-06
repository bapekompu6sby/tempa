<?php

// Bootstraps Laravel and marks all EventInstruction.checked = true for
// Events where status = 'selesai'. Prints a per-event summary.

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap the application
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Event;
use App\Models\EventInstruction;
use Illuminate\Support\Facades\DB;

echo "Starting check_selesai_event_instructions: bootstrapping Laravel\n";

$events = Event::whereIn('status', ['selesai', 'pelaporan', 'pelaksanaan'])->cursor();
$processed = 0;
$updatedInstructions = 0;

foreach ($events as $event) {
    $processed++;
    // Decide which phases to mark as checked based on event status
    $status = $event->status;
    $toMark = [];

    if ($status === 'selesai') {
        // mark any unchecked instruction for the event
        // we'll collect all unchecked across phases
        $instructions = $event->eventInstructions()->where('checked', false)->get();
        $count = $instructions->count();
        if ($count === 0) {
            echo "Event {$event->id} ({$event->name}) - already all checked.\n";
            continue;
        }

        DB::transaction(function () use ($instructions, &$updatedInstructions) {
            foreach ($instructions as $ins) {
                $ins->checked = true;
                $ins->save();
                $updatedInstructions++;
            }
        });

        echo "[Updated] Event {$event->id} ({$event->name}): set {$count} instruction(s) to checked (status=selesai).\n";
        continue;
    }

    if ($status === 'pelaporan') {
        $toMark = ['persiapan', 'pelaksanaan'];
    } elseif ($status === 'pelaksanaan') {
        $toMark = ['persiapan'];
    }

    if (!empty($toMark)) {
        // Use Event model helper which performs an efficient bulk update
        $countUpdated = $event->markInstructionsCheckedForPhases($toMark);
        if ($countUpdated > 0) {
            $updatedInstructions += $countUpdated;
            echo "[Updated] Event {$event->id} ({$event->name}): set {$countUpdated} instruction(s) to checked for phases [" . implode(',', $toMark) . "] (status={$status}).\n";
        } else {
            echo "Event {$event->id} ({$event->name}) - no unchecked instructions in phases [" . implode(',', $toMark) . "].\n";
        }
    }
}

echo "Finished. Processed {$processed} events; updated {$updatedInstructions} instruction(s).\n";
