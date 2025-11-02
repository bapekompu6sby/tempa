<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Backfill existing event_documents.file_path into event_document_files.
     *
     * This migration will:
     *  - For each event_documents row with a non-null file_path, create a row
     *    in event_document_files preserving original_name, file_path, mime, size.
     *  - Then set event_documents.file_path to NULL to avoid duplication.
     *
     * @return void
     */
    public function up()
    {
        $rows = DB::table('event_documents')->whereNotNull('file_path')->get();

        foreach ($rows as $row) {
            $path = $row->file_path;
            if (empty($path)) continue;

            // Only insert if file_path exists on disk or even if not we'll still preserve the reference
            $mime = null;
            $size = null;
            try {
                if (Storage::disk('public')->exists($path)) {
                    $mime = Storage::disk('public')->mimeType($path);
                    $size = Storage::disk('public')->size($path);
                }
            } catch (\Exception $e) {
                // ignore storage errors and proceed with null mime/size
            }

            DB::table('event_document_files')->insert([
                'event_document_id' => $row->id,
                'original_name' => basename($path),
                'file_path' => $path,
                'mime' => $mime,
                'size' => $size,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // null out the original column to avoid duplication
            DB::table('event_documents')->where('id', $row->id)->update(['file_path' => null]);
        }
    }

    /**
     * Attempt to restore a single file back onto event_documents.file_path
     * for each document and then delete the event_document_files rows.
     * This is a best-effort reverse; use with caution.
     *
     * @return void
     */
    public function down()
    {
        $files = DB::table('event_document_files')->get();
        // group by event_document_id and restore the first file_path if event_documents.file_path is null
        $grouped = [];
        foreach ($files as $f) {
            $grouped[$f->event_document_id][] = $f;
        }

        foreach ($grouped as $docId => $list) {
            $first = $list[0];
            $current = DB::table('event_documents')->where('id', $docId)->value('file_path');
            if (empty($current)) {
                DB::table('event_documents')->where('id', $docId)->update(['file_path' => $first->file_path]);
            }
        }

        // delete all rows we created / that exist now
        DB::table('event_document_files')->delete();
    }
};
