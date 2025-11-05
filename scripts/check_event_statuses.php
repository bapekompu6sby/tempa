<?php
/**
 * scripts/check_event_statuses.php
 *
 * Run this from project root:
 * php scripts/check_event_statuses.php
 *
 * Bootstraps the Laravel application and runs Event::check_status() for
 * every Event using a cursor to avoid high memory usage.
 */

declare(strict_types=1);

use App\Models\Event;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

// Bootstrap the application (console kernel) so Eloquent and config are available
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting check_event_statuses: bootstrapped Laravel\n";

$total = 0;
$updated = 0;

try {
    foreach (Event::cursor() as $event) {
        $total++;
        $before = $event->status ?? null;

        try {
            $event->check_status();
            $event->refresh();
        } catch (\Throwable $e) {
            echo "[Error] Event {$event->id} failed to check_status: {$e->getMessage()}\n";
            continue;
        }

        $after = $event->status ?? null;
        if ($before !== $after) {
            $updated++;
            echo "[Updated] Event {$event->id}: '{$before}' -> '{$after}'\n";
        }
    }

    echo "Finished. Processed {$total} events, updated {$updated} statuses.\n";
    exit(0);

} catch (\Throwable $e) {
    echo "Fatal error while processing events: {$e->getMessage()}\n";
    exit(2);
}
