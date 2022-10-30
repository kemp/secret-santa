<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [\App\Http\Controllers\PartyController::class, 'create'])->name('party.create');
Route::post('/', [\App\Http\Controllers\PartyController::class, 'store'])->name('party.store')->middleware('throttle:3,1');
Route::get('/party/{party:invitation_token}', [\App\Http\Controllers\PartyController::class, 'show'])->name('party.show');
Route::post('/party/{party:invitation_token}/participants', [\App\Http\Controllers\PartyController::class, 'addParticipant'])->name('party.participants.store');
Route::post('/party/{party:invitation_token}/initiate', [\App\Http\Controllers\PartyController::class, 'initiate'])->name('party.initiate');
