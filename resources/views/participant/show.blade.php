@php
    /**
     * @var $participant \App\Models\Participant
     * @var $party \App\Models\Party
     * @var $recipient \App\Models\Participant|null
     */
@endphp

@extends('layouts.app')

@section('title', "Your Secret Santa {$party->year}")

@section('main')
    <section class="card">
        <div class="flex items-center gap-4">
            @include('avatar', ['name' => $participant->name, 'class' => 'size-12 text-lg'])
            <div>
                <h1 class="text-2xl sm:text-3xl">Hi, {{ $participant->name }}!</h1>
                <p class="hint">{{ $party->creator()->name }}'s Secret Santa, {{ $party->year }}</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center gap-x-3 gap-y-2 border-t border-stone-200 pt-5 text-sm dark:border-white/10">
            <span class="text-berry-600 dark:text-berry-300">@include('icon', ['name' => 'lock', 'class' => 'size-5'])</span>
            <span class="min-w-48 flex-1 text-stone-600 dark:text-stone-300">This page is private to you. Bookmark it, and don't share the link.</span>
            <input type="hidden" id="private_link" value="{{ route('participant.show', $participant) }}">
            <button type="button" class="btn btn-ghost px-3 py-1.5 text-sm" data-copy="#private_link">
                @include('icon', ['name' => 'copy', 'class' => 'size-4'])
                Copy my link
            </button>
        </div>

        @if($participant->edit_token && ! $party->began_at)
            <a class="btn btn-secondary mt-4 w-full" href="{{ route('party.show', ['party' => $party, 'edit_token' => $participant->edit_token]) }}">
                Manage the party &amp; draw names
                @include('icon', ['name' => 'right', 'class' => 'size-4'])
            </a>
        @elseif(! $party->began_at)
            <a class="mt-4 inline-flex items-center gap-1.5 font-bold text-pine-700 hover:underline dark:text-pine-300" href="{{ route('party.show', $party) }}">
                See who's joined
                @include('icon', ['name' => 'right', 'class' => 'size-4'])
            </a>
        @endif
    </section>

    @if($recipient)
        <section class="space-y-5 pt-4">
            <h2 class="text-center text-xl text-stone-600 dark:text-stone-300">
                You're the Secret Santa for
                <span class="animate-reveal mt-1 block font-display text-5xl text-berry-700 dark:text-berry-300">{{ $recipient->name }}</span>
            </h2>

            @include('participant.profile-card', ['person' => $recipient, 'profile' => $profile])
        </section>
    @elseif(! $party->began_at)
        <section class="card flex items-start gap-4">
            <span class="mt-1 text-pine-600 dark:text-pine-300">@include('icon', ['name' => 'clock', 'class' => 'size-6'])</span>
            <div>
                <h2 class="text-xl">Names haven't been drawn yet</h2>
                <p class="hint mt-1">{{ $party->participants()->count() }} people have joined so far. Once {{ $party->creator()->name }} draws names, you'll get an email and this page will show who you're buying for.</p>
            </div>
        </section>
    @endif

    @include('participant.questionnaire')
@endsection
