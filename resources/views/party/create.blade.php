@extends('layouts.app')

@section('main')
    <h2>Start your Secret Santa</h2>

    <p class="font-semibold">How it works:</p>

    <ol class="mb-4 list-decimal list-outside ml-4">
        <li>Use the form below to create a Secret Santa</li>
        <li>Share the link on the following page with the people you would like to invite</li>
        <li>Once everyone has joined, click the link in your email to start the Secret Santa!</li>
    </ol>

    <hr class="mb-4">

    @include('participant-form', ['route' => route('party.store'), 'submitText' => 'Start Secret Santa 🛷'])
@endsection
