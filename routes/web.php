<?php

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

Route::get('/', 'PartyController@create')->name('party.create');
Route::post('/', 'PartyController@store')->name('party.store');

Route::get('participant/{participant}', 'ParticipantController@show')->name('participant.show');
Route::post('participant/{participant}/confirm', 'ParticipantController@confirm')->name('participant.confirm');

Route::get('mailable/initiated', function () {
    $invoice = App\SecretSanta::find(1);

    return new App\Mail\SecretSantaInitiated($invoice);
});

Route::get('mailable/invited', function () {
    $participant = App\Participant::find(1);
    $party = App\Party::find(1);

    return new App\Mail\ParticipantInvited($participant, $party);
});
