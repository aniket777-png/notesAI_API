<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NoteController;

Route::middleware('throttle:notes-api')->group(function () {

    // CRUD routes (already correct)
    Route::apiResource('notes', NoteController::class);

    // summary route (OK)
    Route::post('notes/{note}/summary', [NoteController::class, 'summary']);

    // search route (KEEP IT LIKE THIS - GOOD)
    Route::get('search/notes', [NoteController::class, 'search']);
    // Route::get('search/notes', [NoteController::class, 'search']);
    Route::post('notes/{id}/summary', [NoteController::class, 'summary']);

});