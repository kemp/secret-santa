@extends('layouts.app')

@section('main')
    <section class="card">
        <p class="eyebrow">{{ now()->year }} edition</p>
        <h1 class="mt-2 text-3xl font-semibold text-pine-900 sm:text-4xl dark:text-white">Start your Secret Santa</h1>
        <p class="mt-3 text-stone-600 dark:text-stone-300">No accounts, no apps. Just a link for the family and a few fun questions so everyone knows what to get.</p>

        <ol class="mt-6 grid gap-3 sm:grid-cols-3">
            @foreach ([
                ['🔗', 'Share the link', 'Send the invite link to everyone taking part.'],
                ['📝', 'Everyone answers', 'Each person fills in a short, optional questionnaire.'],
                ['🎲', 'Draw names', 'Start the draw from your email. Everyone finds out who they\'re buying for.'],
            ] as [$icon, $title, $text])
                <li class="rounded-2xl bg-pine-50 p-4 dark:bg-white/5">
                    <p class="text-xl" aria-hidden="true">{{ $icon }}</p>
                    <p class="mt-1 font-semibold">{{ $loop->iteration }}. {{ $title }}</p>
                    <p class="hint mt-1">{{ $text }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="card">
        <h2 class="mb-4 text-xl font-semibold">You're the organiser</h2>

        @include('participant-form', [
            'route' => route('party.store'),
            'submitText' => 'Create Secret Santa 🛷',
            'suggestedDeadline' => $suggestedDeadline,
        ])
    </section>
@endsection
