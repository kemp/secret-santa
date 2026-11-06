@php
    /**
     * @var $party \App\Models\Party
     */
    $creator = $party->creator();
    $isManager = $party->isManagedByRequest();
@endphp

@extends('layouts.app')

@section('title', "{$creator->name} has invited you to join Secret Santa!")

@section('main')
    <section class="card">
        <p class="eyebrow">Secret Santa {{ $party->year }}</p>
        <h1 class="mt-2 text-3xl font-semibold text-pine-900 dark:text-white">{{ $creator->name }} has invited you to join Secret Santa!</h1>

        <div class="mt-6">
            <label class="label" for="invitation_link">Share this link with others</label>
            <div class="flex gap-2">
                <input
                    type="text"
                    id="invitation_link"
                    class="field min-w-0 flex-1 bg-stone-50 font-mono text-sm dark:bg-white/5"
                    readonly
                    onfocus="this.select()"
                    value="{{ route('party.show', $party) }}"
                >
                <button type="button" class="btn btn-ghost" data-copy="#invitation_link" data-share-title="Join our Secret Santa">Copy</button>
            </div>
        </div>

        <div class="mt-6">
            <p class="label">{{ $party->participants->count() }} {{ \Illuminate\Support\Str::plural('person', $party->participants->count()) }} joined</p>
            <ul class="flex flex-wrap gap-2">
                @foreach($party->participants as $participant)
                    <li class="flex items-center gap-2 rounded-full bg-stone-900/5 py-1 pr-3 pl-1 text-sm font-medium dark:bg-white/10">
                        @include('avatar', ['name' => $participant->name, 'class' => 'size-7 text-xs ring-0'])
                        {{ $participant->name }}
                    </li>
                @endforeach
            </ul>
        </div>

        @if($party->deadline)
            <p class="hint mt-4">📅 Questionnaires close {{ $party->deadline->format('l, F j') }}.</p>
        @endif
    </section>

    @if($party->began_at)
        <section class="card text-center">
            <p class="text-4xl" aria-hidden="true">🎉</p>
            <h2 class="mt-2 text-2xl font-semibold">The Secret Santa has begun!</h2>
            <p class="hint mt-2">Everyone has been emailed who they're buying for. Use your private link to see your person's answers.</p>
        </section>
    @else
        @if($party->canBeEdited())
            <section class="card space-y-6">
                <div>
                    <p class="eyebrow">Organiser tools</p>
                    <h2 class="mt-1 text-2xl font-semibold">Exclusions</h2>
                    <p class="hint mt-1">Pairs who shouldn't draw each other, like partners.</p>
                </div>

                @if($party->exclusions->isNotEmpty())
                    <ul class="divide-y divide-stone-200 rounded-2xl border border-stone-200 dark:divide-white/10 dark:border-white/10">
                        @foreach($party->exclusions as $exclusion)
                            <li class="flex items-center justify-between gap-2 px-4 py-2">
                                <span>{{ $exclusion->from->name }} <span class="text-stone-400" aria-label="and">&harr;</span> {{ $exclusion->to->name }}</span>
                                <form action="{{ route('exclusions.destroy', [$party, $exclusion]) }}" method="POST">
                                    @method('DELETE')
                                    @csrf

                                    <button class="btn-icon btn" type="submit" title="Remove exclusion">&times;</button>
                                </form>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if($party->participants->count() > 1)
                    <form action="{{ route('exclusions.store', $party) }}" method="POST" class="flex items-center gap-2">
                        @csrf

                        <label class="sr-only" for="from_id">First person</label>
                        <select class="field min-w-0 flex-1" name="from_id" id="from_id">
                            @foreach($party->participants as $participant)
                                <option value="{{ $participant->id }}">{{ $participant->name }}</option>
                            @endforeach
                        </select>
                        <span class="text-stone-400" aria-hidden="true">&harr;</span>
                        <label class="sr-only" for="to_id">Second person</label>
                        <select class="field min-w-0 flex-1" name="to_id" id="to_id">
                            @foreach($party->participants as $participant)
                                <option value="{{ $participant->id }}" @selected($loop->index === 1)>{{ $participant->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-ghost" type="submit" title="Save exclusion">Add</button>
                    </form>
                @endif

                @include('party.deadline-form')

                @if($party->canBeInitiated())
                    <form action="{{ route('party.initiate', ['party' => $party, 'edit_token' => request()->edit_token]) }}" method="POST"
                          onsubmit="return confirm('Draw names now? Everyone will be emailed and no one else can join.')">
                        @csrf

                        <button type="submit" class="btn btn-secondary w-full py-3 text-base">🎲 Draw names &amp; email everyone</button>
                    </form>
                @else
                    <p class="hint rounded-2xl bg-stone-900/5 p-4 text-center dark:bg-white/5">You can draw names once at least 3 people have joined.</p>
                @endif
            </section>
        @endif

        <section class="card">
            <h2 class="mb-1 text-2xl font-semibold">Want to join?</h2>
            <p class="hint mb-5">Pop in your name and email. Next, you'll get a few optional questions to help your Secret Santa.</p>

            @include('participant-form', ['route' => route('party.participants.store', $party), 'submitText' => 'Join Secret Santa'])
        </section>
    @endif

    @if($isManager && $party->began_at)
        <section class="card">
            @include('party.deadline-form')
        </section>
    @endif

    <details class="card group">
        <summary class="cursor-pointer list-none font-semibold marker:hidden">
            <span class="flex items-center justify-between">
                Already joined? Get your private link again
                <span class="text-stone-400 transition group-open:rotate-45" aria-hidden="true">+</span>
            </span>
        </summary>

        <form action="{{ route('party.resend-link', $party) }}" method="POST" class="mt-4 flex gap-2">
            @csrf

            <label class="sr-only" for="resend_email">Your email</label>
            <input class="field min-w-0 flex-1" type="email" name="email" id="resend_email" placeholder="you@example.com" autocomplete="email" required>
            <button class="btn btn-ghost" type="submit">Email me my link</button>
        </form>
    </details>
@endsection
