@extends('layouts.app')

@section('main')
    <section class="card">
        <h1 class="text-3xl sm:text-4xl">Start the {{ now()->year }} Secret Santa</h1>
        <p class="mt-3 text-stone-600 dark:text-stone-300">Everyone joins from one link and answers a few short questions, so whoever draws their name knows what to get. Nobody needs an account.</p>

        <ol class="mt-6 space-y-3">
            @foreach ([
                ['Share the link', 'Send the invite link to everyone taking part.'],
                ['Everyone answers', 'Each person fills in a short questionnaire. Every question is optional.'],
                ['Draw names', 'You start the draw from your email, and everyone finds out who they\'re buying for.'],
            ] as [$title, $text])
                <li class="flex gap-3">
                    <span class="font-display text-lg leading-snug text-berry-600 tabular-nums dark:text-berry-300">{{ $loop->iteration }}.</span>
                    <p><span class="font-bold">{{ $title }}.</span> <span class="text-stone-600 dark:text-stone-300">{{ $text }}</span></p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="card">
        <h2 class="mb-1 text-2xl">You're the organiser</h2>
        <p class="hint mb-5">We'll email you a private link for managing the party and drawing names.</p>

        @include('participant-form', [
            'route' => route('party.store'),
            'submitText' => 'Create the Secret Santa',
            'suggestedDeadline' => $suggestedDeadline,
        ])
    </section>
@endsection
