@extends('layouts.app')

@section('title', 'Questions · Secret Santa admin')
@section('width', 'max-w-3xl')

@section('main')
    <section class="card">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="eyebrow">Admin</p>
                <h1 class="mt-1 text-3xl font-semibold text-pine-900 dark:text-white">Questionnaire</h1>
                <p class="hint mt-1">Questions without a year are asked every year. Give one a year to make it a one-off, or switch it off to rotate it out.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.questions.create') }}">+ Add question</a>
        </div>

        <nav class="mt-6 flex items-center justify-center gap-3" aria-label="Year">
            <a class="btn-icon btn" href="{{ route('admin.questions.index', ['year' => $year - 1]) }}" title="Previous year">&larr;</a>
            <span class="font-display text-xl font-semibold">{{ $year }}</span>
            <a class="btn-icon btn" href="{{ route('admin.questions.index', ['year' => $year + 1]) }}" title="Next year">&rarr;</a>
        </nav>
    </section>

    <section class="card">
        <h2 class="text-xl font-semibold">Asked in {{ $year }} <span class="text-stone-400">({{ $asked->count() }})</span></h2>
        <p class="hint mb-4">This is the questionnaire everyone in a {{ $year }} party sees, in this order.</p>

        @include('admin.questions.list', ['questions' => $asked, 'reorderable' => true])
    </section>

    <section class="card">
        <h2 class="text-xl font-semibold">Not asked in {{ $year }} <span class="text-stone-400">({{ $other->count() }})</span></h2>
        <p class="hint mb-4">Switched off, or set for a different year.</p>

        @include('admin.questions.list', ['questions' => $other, 'reorderable' => false])
    </section>
@endsection
