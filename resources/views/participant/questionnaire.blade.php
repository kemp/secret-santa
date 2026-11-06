@php
    $open = $party->acceptsAnswers();
    $answeredCount = $questions->filter(fn ($question) => filled($answers->get($question->id)))->count();
@endphp

<section class="card" id="questionnaire">
    <div class="flex flex-wrap items-end justify-between gap-2">
        <div>
            <p class="eyebrow">For your Secret Santa</p>
            <h2 class="mt-1 text-2xl font-semibold">Your questionnaire</h2>
        </div>
        @if($questions->isNotEmpty())
            <p class="text-sm font-medium text-stone-500 dark:text-stone-400" aria-live="polite">
                <span data-answered-count>{{ $answeredCount }}</span> of {{ $questions->count() }} answered
            </p>
        @endif
    </div>

    @if($questions->isNotEmpty())
        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-stone-900/5 dark:bg-white/10">
            <div class="h-full rounded-full bg-pine-500 transition-all duration-500" data-progress
                 style="width: {{ round($answeredCount / $questions->count() * 100) }}%"></div>
        </div>
    @endif

    <p class="hint mt-4">
        Every question is optional. Skip anything you like; your Santa only sees what you fill in.
        @if($party->deadline && $open)
            You can come back and change your answers until <strong class="text-stone-700 dark:text-stone-200">{{ $party->deadline->format('l, F j') }}</strong> ({{ $party->deadline->diffForHumans() }}).
        @endif
    </p>

    @unless($open)
        <div class="alert-error mt-4">
            <span aria-hidden="true">🔐</span>
            <p>The questionnaire closed on {{ $party->deadline->format('F j') }}. Here's what you shared.</p>
        </div>
    @endunless

    @if($questions->isEmpty())
        <p class="hint mt-6 rounded-2xl bg-stone-900/5 p-6 text-center dark:bg-white/5">There are no questions this year. Your Santa is flying blind! 🦌</p>
    @else
        @include('errors', ['errors' => $errors])

        <form action="{{ route('participant.answers.update', $participant) }}" method="POST" class="mt-6" data-questionnaire>
            @csrf
            @method('PUT')

            <fieldset class="space-y-6" @disabled(! $open)>
                <legend class="sr-only">Questions</legend>

                @foreach($questions as $question)
                    @php
                        $name = "answers[{$question->id}]";
                        $value = old("answers.{$question->id}", $answers->get($question->id));
                    @endphp

                    <div data-question>
                        @if($question->hasOptions())
                            <fieldset>
                                <legend class="label">{{ $question->text }}</legend>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($question->options as $option)
                                        <label class="choice">
                                            <input type="radio" name="{{ $name }}" value="{{ $option }}" @checked($value === $option)>
                                            <span>{{ $option }}</span>
                                        </label>
                                    @endforeach
                                    <label class="choice">
                                        <input type="radio" name="{{ $name }}" value="" @checked(blank($value))>
                                        <span class="border-dashed">Skip</span>
                                    </label>
                                </div>
                            </fieldset>
                        @else
                            <label class="label" for="answer-{{ $question->id }}">{{ $question->text }}</label>
                            <textarea
                                class="field @error("answers.{$question->id}") field-invalid @enderror"
                                name="{{ $name }}"
                                id="answer-{{ $question->id }}"
                                rows="2"
                                maxlength="2000"
                            >{{ $value }}</textarea>
                        @endif
                    </div>
                @endforeach
            </fieldset>

            @if($open)
                <div class="sticky bottom-4 mt-8">
                    <button class="btn btn-primary w-full py-3 text-base shadow-lg" type="submit">Save my answers</button>
                </div>
            @endif
        </form>
    @endif
</section>
