<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Instruction;
use App\Models\EventInstruction;
use App\Models\DocumentTemplate;
use App\Models\EventDocument;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Event extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'learning_model',
        'start_date',
        'end_date',
        'note',
        'preparation_date',
        'report_date',
        'status',
        'document_drive_url', // URL to documents drive
        'event_report_url', // URL to event report file
        // Added fields
        'target', // integer: target participant count
        'jp_module', // integer: JP for module
        'jp_facilitator', // integer: JP for facilitator
        'field', // string: field/area
    ];

    /**
     * Date casts
     *
     * @var array
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'preparation_date' => 'date',
        'report_date' => 'date',
    ];

    /**
     * Boot the model and register created event listener.
     */
    protected static function booted()
    {
        static::created(function (self $event) {
            // Assign default preparation_date (30 days before start_date) and report_date (14 days after end_date)
            $updates = [];
            try {
                if (!empty($event->start_date)) {
                    $sd = $event->start_date instanceof Carbon ? $event->start_date : Carbon::parse($event->start_date);
                    $prep = $sd->copy()->subDays(30)->toDateString();
                    $updates['preparation_date'] = $prep;
                }
            } catch (\Exception $e) {
                // ignore parse errors
            }
            try {
                if (!empty($event->end_date)) {
                    $ed = $event->end_date instanceof Carbon ? $event->end_date : Carbon::parse($event->end_date);
                    $rep = $ed->copy()->addDays(14)->toDateString();
                    $updates['report_date'] = $rep;
                }
            } catch (\Exception $e) {
                // ignore parse errors
            }

            if (!empty($updates)) {
                // update the model record with computed dates
                $event->update($updates);
                // refresh attributes so subsequent operations see new values
                $event->refresh();
            }

            // Delegate to the model methods to create EventInstruction and EventDocument records
            $event->createEventInstructions();
            $event->createEventDocuments();
        });
    }

    /**
     * Create EventInstruction records for this event based on its learning_model.
     *
     * Logic:
     * 1. Read $this->learning_model (one of allowed columns)
     * 2. Query Instruction where that column is true
     * 3. Create EventInstruction rows duplicating relevant fields from Instruction
     *
     * This method is safe to call multiple times (it only creates entries) —
     * if you want idempotency or syncing behavior, update this method to delete
     * or sync existing EventInstruction records first.
     *
     * @return void
     */
    public function createEventInstructions(): void
    {
        if (empty($this->learning_model)) {
            return;
        }

        $allowed = ['full_elearning', 'distance_learning', 'blended_learning', 'classical'];
        if (!in_array($this->learning_model, $allowed, true)) {
            return;
        }

        $column = $this->learning_model;
        $instructions = Instruction::where($column, true)->get();
        if ($instructions->isEmpty()) {
            return;
        }

        // Use transaction for atomic creation
        DB::transaction(function () use ($instructions) {
            foreach ($instructions as $instruction) {
                EventInstruction::create([
                    'event_id' => $this->id,
                    'instruction_id' => $instruction->id,
                    'checked' => false,
                    'linkable' => $instruction->linkable,
                        'link' => $instruction->link ?? null,
                        'link_label' => $instruction->link_label ?? null,
                        'phase' => $instruction->phase ?? 'pelaksanaan',
                    'full_elearning' => $instruction->full_elearning,
                    'distance_learning' => $instruction->distance_learning,
                    'blended_learning' => $instruction->blended_learning,
                    'classical' => $instruction->classical,
                ]);
            }
        });
    }

    /**
     * Create EventDocument records for this event based on its learning_model.
     *
     * Logic mirrors createEventInstructions: find DocumentTemplate rows where
     * the learning_model column is true and duplicate their attributes into
     * EventDocument entries for this event.
     *
     * @return void
     */
    public function createEventDocuments(): void
    {
        if (empty($this->learning_model)) {
            return;
        }

        $allowed = ['full_elearning', 'distance_learning', 'blended_learning', 'classical'];
        if (!in_array($this->learning_model, $allowed, true)) {
            return;
        }

        $column = $this->learning_model;
        $templates = DocumentTemplate::where($column, true)->get();
        if ($templates->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($templates) {
            foreach ($templates as $tpl) {
                EventDocument::create([
                    'event_id' => $this->id,
                    'name' => $tpl->name,
                    'notes' => null,
                    'full_elearning' => $tpl->full_elearning,
                    'distance_learning' => $tpl->distance_learning,
                    'blended_learning' => $tpl->blended_learning,
                    'classical' => $tpl->classical,
                ]);
            }
        });
    }

    /**
     * Get the EventInstruction records for this Event.
     */
    public function eventInstructions()
    {
        return $this->hasMany(EventInstruction::class);
    }

    /**
     * Get the attached event report file for this Event.
     */
    public function eventReport()
    {
        return $this->hasOne(EventDocument::class, 'event_id')->where('phase', 'event_report');
    }

    /**
     * Get the EventDocument records for this Event.
     */
    public function eventDocuments()
    {
        return $this->hasMany(EventDocument::class);
    }

    /**
     * Count number of EventInstruction rows for this event by phase.
     *
     * @param string $phase
     * @return int
     */
    public function instructionCountByPhase(string $phase): int
    {
        return $this->eventInstructions()->where('phase', $phase)->count();
    }

    /**
     * Count number of checked EventInstruction rows for this event by phase.
     *
     * @param string $phase
     * @return int
     */
    public function checkedInstructionCountByPhase(string $phase): int
    {
        return $this->eventInstructions()->where('phase', $phase)->where('checked', true)->count();
    }

    /**
     * Check and update the event status based on checked EventInstruction rows.
     *
     * Rules:
    * - If current status is one of: 'tentative', 'dibatalkan', 'selesai' => do nothing (exit).
     * - Otherwise determine status by priority: pelaporan -> pelaksanaan -> persiapan.
     *   If any checked instruction exists for a phase, set status to that phase and persist.
     * - If none found, set status back to 'tentative'.
     *
     * @return void
     */
    public function check_status(): void
    {
        $current = $this->status ?? null;

        // do not override final statuses like dibatalkan or selesai
        if (in_array($current, ['dibatalkan', 'selesai'], true)) {
            return;
        }

        $priority = ['pelaporan', 'pelaksanaan', 'persiapan'];
        $newStatus = null;

        foreach ($priority as $phase) {
            try {
                if ($this->checkedInstructionCountByPhase($phase) > 0) {
                    $newStatus = $phase;
                    break;
                }
            } catch (\Exception $e) {
                // if relationship not available or query fails, continue
                continue;
            }
        }

        // No checked instructions found
        if (is_null($newStatus)) {
            // Total checked across phases
            $checkedTotal = 0;
            foreach ($priority as $p) {
                try {
                    $checkedTotal += $this->checkedInstructionCountByPhase($p);
                } catch (\Exception $e) {
                    // ignore
                }
            }

            // If there are absolutely no checked instructions, mark as 'belum_dimulai'
            if ($checkedTotal === 0) {
                $newStatus = 'belum_dimulai';
            } else {
                // fallback when counts exist but no priority phase matched
                $newStatus = 'tentative';
            }
        }

        // Prevent downgrading from 'belum_dimulai' to 'tentative'
        if ($current === 'belum_dimulai' && $newStatus === 'tentative') {
            $newStatus = 'belum_dimulai';
        }

        if ($newStatus !== $current) {
            // persist change and then ensure related instructions are checked
            DB::transaction(function () use ($newStatus) {
                $this->update(['status' => $newStatus]);

                // If event moved into 'pelaksanaan' ensure all 'persiapan' instructions are checked
                if ($newStatus === 'pelaksanaan') {
                    $this->markInstructionsCheckedForPhases(['persiapan']);
                }

                // If event moved into 'pelaporan' ensure both 'persiapan' and 'pelaksanaan' instructions are checked
                if ($newStatus === 'pelaporan') {
                    $this->markInstructionsCheckedForPhases(['persiapan', 'pelaksanaan']);
                }
            });

            // refresh model state after transaction
            $this->refresh();
        }
    }

    /**
     * Mark all EventInstruction rows for the given phases as checked.
     * Returns the number of instructions updated.
     *
     * @param array $phases
     * @return int
     */
    public function markInstructionsCheckedForPhases(array $phases): int
    {
        if (empty($phases)) return 0;

        // Use the relation's query builder to perform an efficient bulk update.
        $query = $this->eventInstructions()->whereIn('phase', $phases)->where('checked', false);
        try {
            $updated = $query->update(['checked' => true]);
            return (int) $updated;
        } catch (\Exception $e) {
            // On failure, return 0 — caller should continue gracefully.
            return 0;
        }
    }
}
