@php
    $open = $party->acceptsAnswers();
    $answeredCount = $questions->filter(fn ($question) => filled($answers->get($question->id)))->count();
@endphp

<section class="card" id="questionnaire">
    <div class="flex flex-wrap items-end justify-between gap-2">
        <h2 class="text-2xl">Your questionnaire</h2>
        @if($questions->isNotEmpty())
            <p class="text-sm text-stone-600 tabular-nums dark:text-stone-300" aria-live="polite">
                <span data-answered-count>{{ $answeredCount }}</span> of {{ $questions->count() }} answered
            </p>
        @endif
    </div>

    @if($questions->isNotEmpty())
        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-stone-900/5 dark:bg-white/10">
            <div class="h-full rounded-full bg-pine-500 transition-all duration-500" data-progress
                 style="width: {{ round($answeredCount / $questions->count() * 100) }}%; transition-timing-function: var(--ease-out-expo)"></div>
        </div>
    @endif

    <p class="hint mt-4">
        These help whoever draws your name. Every question is optional, and your Santa only sees what you fill in.
        @if($party->deadline && $open)
            You can come back and change your answers until <strong class="text-stone-700 dark:text-stone-200">{{ $party->deadline->format('l, F j') }}</strong> ({{ $party->deadline->diffForHumans() }}).
        @endif
    </p>

    @unless($open)
        <div class="alert alert-error mt-4">
            @include('icon', ['name' => 'lock', 'class' => 'mt-0.5 size-5'])
            <p>The questionnaire closed on {{ $party->deadline->format('F j') }}, so answers can't be changed. Here's what you shared.</p>
        </div>
    @endunless

    @if($questions->isEmpty())
        <p class="hint mt-6 text-center">There are no questions this year, so your Santa will have to improvise.</p>
    @else
        @include('errors', ['errors' => $errors])

        <form action="{{ route('participant.answers.update', $participant) }}" method="POST" class="mt-6" data-questionnaire>
            @csrf
            @method('PUT')

            <fieldset class="space-y-7" @disabled(! $open)>
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
