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
                <p class="eyebrow">{{ $party->creator()->name }}'s Secret Santa &middot; {{ $party->year }}</p>
                <h1 class="text-2xl font-semibold text-pine-900 sm:text-3xl dark:text-white">Hi, {{ $participant->name }}!</h1>
            </div>
        </div>

        <div class="mt-5 flex flex-wrap items-center gap-2 rounded-2xl bg-gold-300/25 p-3 text-sm text-stone-700 dark:bg-gold-400/10 dark:text-stone-200">
            <span aria-hidden="true">🔒</span>
            <span class="flex-1">This is your private page. Bookmark it, and don't share the link.</span>
            <input type="hidden" id="private_link" value="{{ route('participant.show', $participant) }}">
            <button type="button" class="btn btn-ghost px-3 py-1.5 text-sm" data-copy="#private_link">Copy my link</button>
        </div>

        @if($participant->edit_token && ! $party->began_at)
            <a class="btn btn-secondary mt-4 w-full" href="{{ route('party.show', ['party' => $party, 'edit_token' => $participant->edit_token]) }}">
                Manage the party &amp; draw names &rarr;
            </a>
        @elseif(! $party->began_at)
            <a class="mt-4 inline-block text-sm font-medium text-pine-700 underline-offset-4 hover:underline dark:text-pine-300" href="{{ route('party.show', $party) }}">
                See who's joined &rarr;
            </a>
        @endif
    </section>

    @if($recipient)
        <section class="space-y-4">
            <div class="text-center">
                <p class="eyebrow">You're the Secret Santa for</p>
                <p class="mt-1 font-display text-4xl font-semibold text-berry-700 dark:text-berry-300">{{ $recipient->name }} 🎁</p>
            </div>

            @include('participant.profile-card', ['person' => $recipient, 'profile' => $profile])
        </section>
    @elseif(! $party->began_at)
        <section class="card flex items-start gap-4">
            <span class="text-3xl" aria-hidden="true">⏳</span>
            <div>
                <h2 class="text-lg font-semibold">Names haven't been drawn yet</h2>
                <p class="hint mt-1">{{ $party->participants()->count() }} people have joined so far. Once {{ $party->creator()->name }} draws names, you'll get an email and this page will show who you're buying for.</p>
            </div>
        </section>
    @endif

    @include('participant.questionnaire')
@endsection
