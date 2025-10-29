<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Instruction;
use App\Models\EventInstruction;
use App\Models\DocumentTemplate;
use App\Models\EventDocument;
use Illuminate\Support\Facades\DB;

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
    ];

    /**
     * Boot the model and register created event listener.
     */
    protected static function booted()
    {
        static::created(function (self $event) {
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
}
