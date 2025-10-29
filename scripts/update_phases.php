<?php
// scripts/update_phases.php
// Run with: php scripts/update_phases.php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Instruction;
use App\Models\EventInstruction;

echo "Starting phase normalization...\n";

// mapping groups
$toPelaksanaan = ['pembukaan_pelatihan', 'penutupan_pelatihan'];
$toPelaporan = ['evaluasi_pelatihan', 'pasca_pelatihan'];

// simple CLI flags
$dry = in_array('--dry-run', $argv);
if ($dry) {
	echo "DRY RUN: no changes will be made. Use without --dry-run to apply updates.\n";
}

// helper to count matches
$countMatches = function ($model, $values) {
	return $model::whereIn('phase', $values)->count();
};

// show counts before
$insBefore1 = $countMatches(Instruction::class, $toPelaksanaan);
$insBefore2 = $countMatches(Instruction::class, $toPelaporan);
$eiBefore1 = $countMatches(EventInstruction::class, $toPelaksanaan);
$eiBefore2 = $countMatches(EventInstruction::class, $toPelaporan);

echo "Instructions matching pembukaan/penutupan -> pelaksanaan: {$insBefore1}\n";
echo "Instructions matching evaluasi/pasca -> pelaporan: {$insBefore2}\n";
echo "EventInstructions matching pembukaan/penutupan -> pelaksanaan: {$eiBefore1}\n";
echo "EventInstructions matching evaluasi/pasca -> pelaporan: {$eiBefore2}\n";

if (!$dry) {
	// Instructions
	$count1 = Instruction::whereIn('phase', $toPelaksanaan)->update(['phase' => 'pelaksanaan']);
	echo "Updated instructions to 'pelaksanaan': {$count1}\n";

	$count2 = Instruction::whereIn('phase', $toPelaporan)->update(['phase' => 'pelaporan']);
	echo "Updated instructions to 'pelaporan': {$count2}\n";

	// EventInstructions
	$count3 = EventInstruction::whereIn('phase', $toPelaksanaan)->update(['phase' => 'pelaksanaan']);
	echo "Updated event_instructions to 'pelaksanaan': {$count3}\n";

	$count4 = EventInstruction::whereIn('phase', $toPelaporan)->update(['phase' => 'pelaporan']);
	echo "Updated event_instructions to 'pelaporan': {$count4}\n";

	echo "Phase normalization finished.\n";
} else {
	echo "Dry run complete. No changes applied.\n";
}

return 0;
