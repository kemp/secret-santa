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
Route::post('/', 'PartyController@store')->name('party.store')->middleware('throttle:3,1');
Route::get('/party/{party:invitation_token}', 'PartyController@show')->name('party.show');
Route::post('/party/{party:invitation_token}/participants', 'PartyController@addParticipant')->name('party.participants.store');
Route::post('/party/{party:invitation_token}/initiate', 'PartyController@initiate')->name('party.initiate');
