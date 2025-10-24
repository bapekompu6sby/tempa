<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('instructions', App\Http\Controllers\InstructionController::class);
Route::resource('events', App\Http\Controllers\EventController::class);
Route::resource('documents', App\Http\Controllers\EventDocumentController::class);
