<?php
use Illuminate\Support\Facades\Route; use App\Http\Controllers\Api\NoteController;
Route::middleware('throttle:60,1')->group(function(){
Route::apiResource('notes',NoteController::class);
Route::post('notes/{id}/summary',[NoteController::class,'summary']);
Route::get('notes-search',[NoteController::class,'search']);
});
