<?php
/**
 * scripts/update_event_document_checks.php
 *
 * Run from project root:
 *     php scripts/update_event_document_checks.php
 *
 * This bootstraps Laravel and then iterates through all EventDocument
 * records (with their attachments) updating the `checked` boolean to
 * true when the document already has a link, a stored file_path, or
 * at least one uploaded attachment; otherwise the flag is set to false.
 *
 * The intention is to backfill/normalize the new column after migration.
 */

declare(strict_types=1);

use App\Models\EventDocument;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

// Bootstrap the console kernel to load Eloquent, config, etc.
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "Starting update_event_document_checks: bootstrapped Laravel\n";

$total = 0;
$changed = 0;

try {
    // include both files and parent event (for start_date) to avoid N+1
    foreach (EventDocument::with(['files','event'])->cursor() as $doc) {
        $total++;

        // base check: link, stored file, or any uploaded attachment
        $hasAttachment = false;
        if (!empty($doc->link)) {
            $hasAttachment = true;
        }
        if (!$hasAttachment && !empty($doc->file_path)) {
            $hasAttachment = true;
        }
        if (!$hasAttachment) {
            // use files count directly (we eager-loaded relationship)
            if ($doc->files && $doc->files->count() > 0) {
                $hasAttachment = true;
            }
        }

        // override: if the parent event starts in 2025, consider this document checked
        if (!$hasAttachment && $doc->event && $doc->event->start_date) {
            try {
                $year = \Carbon\Carbon::parse($doc->event->start_date)->year;
                if ($year === 2025) {
                    $hasAttachment = true;
                }
            } catch (\Exception $e) {
                // ignore parse errors and leave hasAttachment as-is
            }
        }

        $desired = (bool) $hasAttachment;
        if ($doc->checked !== $desired) {
            $doc->checked = $desired;
            $doc->save();
            $changed++;
            echo "[Updated] EventDocument {$doc->id} -> checked={$desired}\n";
        }
    }

    echo "Finished. Processed {$total} documents, updated {$changed} flags.\n";
    exit(0);
} catch (\Throwable $e) {
    echo "Fatal error while processing documents: {$e->getMessage()}\n";
    exit(2);
}
