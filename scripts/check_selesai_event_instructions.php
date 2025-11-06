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

$events = Event::where('status', 'selesai')->cursor();
$processed = 0;
$updatedInstructions = 0;

foreach ($events as $event) {
    $processed++;
    $instructions = $event->eventInstructions()->where('checked', false)->get();
    $count = $instructions->count();
    if ($count === 0) {
        echo "Event {$event->id} ({$event->name}) - already all checked.\n";
        continue;
    }

    DB::transaction(function () use ($event, $instructions, &$updatedInstructions) {
        foreach ($instructions as $ins) {
            $ins->checked = true;
            $ins->save();
            $updatedInstructions++;
        }
        // After toggling instructions, ensure parent event status remains 'selesai'
        // (do not call check_status which may override final statuses)
    });

    echo "[Updated] Event {$event->id} ({$event->name}): set {$count} instruction(s) to checked.\n";
}

echo "Finished. Processed {$processed} events; updated {$updatedInstructions} instruction(s).\n";
