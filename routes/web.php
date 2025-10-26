<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('instructions', App\Http\Controllers\InstructionController::class);
Route::resource('events', App\Http\Controllers\EventController::class);
// view event documents
Route::get('events/{event}/documents', [App\Http\Controllers\EventController::class, 'documents'])->name('events.documents');
// Use 'eventDocument' as the route parameter name so it matches controller method signatures
Route::resource('documents', App\Http\Controllers\EventDocumentController::class)->parameters([
    'documents' => 'eventDocument'
]);
// upload file for event document
Route::post('documents/{eventDocument}/upload', [App\Http\Controllers\EventDocumentController::class, 'upload'])->name('documents.upload');
// EventInstruction toggle route
Route::patch('event-instructions/{eventInstruction}/toggle', [App\Http\Controllers\EventInstructionController::class, 'toggle'])->name('event-instructions.toggle');
// EventInstruction update route (used for saving link and other inline updates)
Route::patch('event-instructions/{eventInstruction}', [App\Http\Controllers\EventInstructionController::class, 'update'])->name('event-instructions.update');
