@php
    /** @var $question \App\Models\Question */
    $editing = $question->exists;
@endphp

@extends('layouts.app')

@section('title', ($editing ? 'Edit question' : 'Add question').' · Secret Santa admin')

@section('main')
    <section class="card">
        <a class="inline-flex items-center gap-1.5 text-sm font-bold text-pine-700 hover:underline dark:text-pine-300" href="{{ route('admin.questions.index') }}">
            @include('icon', ['name' => 'left', 'class' => 'size-4'])
            All questions
        </a>
        <h1 class="mt-4 mb-6 text-3xl">{{ $editing ? 'Edit question' : 'Add a question' }}</h1>

        @include('errors', ['errors' => $errors])

        <form action="{{ $editing ? route('admin.questions.update', $question) : route('admin.questions.store') }}" method="POST" class="space-y-5">
            @csrf
            @if($editing)
                @method('PUT')
            @endif

            <div>
                <label class="label" for="text">Question</label>
                <input class="field @error('text') field-invalid @enderror" type="text" name="text" id="text" required maxlength="255"
                       value="{{ old('text', $question->text) }}" placeholder="What's your go-to comfort snack?">
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label class="label" for="year">Year</label>
                    <input class="field @error('year') field-invalid @enderror" type="number" name="year" id="year" min="2000" max="2100"
                           value="{{ old('year', $question->year) }}" placeholder="Every year">
                    <p class="hint mt-1.5">Leave empty to ask it every year.</p>
                </div>
                <div>
                    <label class="label" for="sort_order">Sort order</label>
                    <input class="field @error('sort_order') field-invalid @enderror" type="number" name="sort_order" id="sort_order" min="0" required
                           value="{{ old('sort_order', $question->sort_order) }}">
                    <p class="hint mt-1.5">Lower numbers come first.</p>
                </div>
            </div>

            <div>
                <label class="label" for="options">Choices <span class="font-normal text-stone-600 dark:text-stone-300">(optional)</span></label>
                <input class="field @error('options') field-invalid @enderror" type="text" name="options" id="options" maxlength="500"
                       value="{{ old('options', implode(', ', $question->options ?? [])) }}" placeholder="Cozy, Practical, Funny, Fancy">
                <p class="hint mt-1.5">Comma-separated. When set, people pick one instead of typing an answer.</p>
            </div>

            <label class="flex items-center gap-3">
                <input type="hidden" name="is_active" value="0">
                <input class="size-5 rounded accent-pine-600" type="checkbox" name="is_active" value="1" @checked(old('is_active', $question->is_active))>
                <span class="font-bold">Asked in the questionnaire</span>
            </label>

            <button class="btn btn-primary w-full py-3" type="submit">{{ $editing ? 'Save changes' : 'Add question' }}</button>
        </form>
    </section>
@endsection
