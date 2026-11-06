@extends('layouts.app')

@section('title', 'Questions · Secret Santa admin')
@section('width', 'max-w-3xl')

@section('main')
    <section class="card">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="text-3xl">Questionnaire admin</h1>
                <p class="hint mt-1 max-w-prose">Questions without a year are asked every year. Give one a year to make it a one-off, or switch it off to rotate it out.</p>
            </div>
            <a class="btn btn-primary" href="{{ route('admin.questions.create') }}">
                @include('icon', ['name' => 'plus', 'class' => 'size-4'])
                Add question
            </a>
        </div>

        <nav class="mt-6 flex items-center justify-center gap-3" aria-label="Year">
            <a class="btn btn-icon" href="{{ route('admin.questions.index', ['year' => $year - 1]) }}" aria-label="Previous year">@include('icon', ['name' => 'left', 'class' => 'size-4'])</a>
            <span class="font-display text-xl tabular-nums">{{ $year }}</span>
            <a class="btn btn-icon" href="{{ route('admin.questions.index', ['year' => $year + 1]) }}" aria-label="Next year">@include('icon', ['name' => 'right', 'class' => 'size-4'])</a>
        </nav>
    </section>

    <section class="card">
        <h2 class="text-xl">Asked in {{ $year }} <span class="text-stone-500 tabular-nums dark:text-stone-400">({{ $asked->count() }})</span></h2>
        <p class="hint mb-4 max-w-prose">This is the questionnaire everyone in a {{ $year }} party sees, in this order.</p>

        @include('admin.questions.list', ['questions' => $asked, 'reorderable' => true])
    </section>

    <section class="card">
        <h2 class="text-xl">Not asked in {{ $year }} <span class="text-stone-500 tabular-nums dark:text-stone-400">({{ $other->count() }})</span></h2>
        <p class="hint mb-4 max-w-prose">Switched off, or set for a different year.</p>

        @include('admin.questions.list', ['questions' => $other, 'reorderable' => false])
    </section>
@endsection
