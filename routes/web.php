<?php

use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\ExclusionController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\PartyController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PartyController::class, 'create'])->name('party.create');
Route::post('/', [PartyController::class, 'store'])->name('party.store')->middleware('throttle:3,1');
Route::get('/party/{party:invitation_token}', [PartyController::class, 'show'])->name('party.show');
Route::post('/party/{party:invitation_token}/participants', [PartyController::class, 'addParticipant'])->name('party.participants.store')->middleware('throttle:10,1');
Route::post('/party/{party:invitation_token}/initiate', [PartyController::class, 'initiate'])->name('party.initiate');
Route::patch('/party/{party:invitation_token}/deadline', [PartyController::class, 'updateDeadline'])->name('party.deadline.update');
Route::post('/party/{party:invitation_token}/resend-link', [PartyController::class, 'resendLink'])->name('party.resend-link')->middleware('throttle:3,1');

Route::post('/party/{party:invitation_token}/exclusions', [ExclusionController::class, 'store'])
    ->name('exclusions.store');

Route::delete('/party/{party:invitation_token}/exclusions/{exclusion}', [ExclusionController::class, 'destroy'])
    ->name('exclusions.destroy');

// A participant's private page (questionnaire + their recipient's profile)
Route::get('/you/{participant}', [ParticipantController::class, 'show'])->name('participant.show');
Route::put('/you/{participant}/answers', [ParticipantController::class, 'updateAnswers'])->name('participant.answers.update')->middleware('throttle:20,1');

// Question admin, protected by HTTP Basic auth (see config/santa.php)
Route::prefix('admin')->name('admin.')->middleware(['throttle:60,1', 'admin'])->group(function () {
    Route::redirect('/', '/admin/questions');
    Route::resource('questions', QuestionController::class)->except(['show', 'destroy']);
    Route::post('questions/{question}/toggle', [QuestionController::class, 'toggle'])->name('questions.toggle');
    Route::post('questions/{question}/move/{direction}', [QuestionController::class, 'move'])
        ->whereIn('direction', ['up', 'down'])
        ->name('questions.move');
});
