@if($questions->isEmpty())
    <p class="hint border-y border-stone-200 py-4 text-center dark:border-white/10">No questions here.</p>
@else
    <ol class="divide-y divide-stone-200 border-y border-stone-200 dark:divide-white/10 dark:border-white/10">
        @foreach($questions as $question)
            <li class="flex flex-wrap items-center gap-3 py-3 sm:flex-nowrap {{ $question->is_active ? '' : 'opacity-60' }}">
                @if($reorderable)
                    <div class="flex flex-col">
                        <form action="{{ route('admin.questions.move', [$question, 'up']) }}" method="POST">
                            @csrf
                            <button class="btn btn-icon size-7" type="submit" aria-label="Move up" @disabled($loop->first)>@include('icon', ['name' => 'up', 'class' => 'size-4'])</button>
                        </form>
                        <form action="{{ route('admin.questions.move', [$question, 'down']) }}" method="POST">
                            @csrf
                            <button class="btn btn-icon size-7" type="submit" aria-label="Move down" @disabled($loop->last)>@include('icon', ['name' => 'down', 'class' => 'size-4'])</button>
                        </form>
                    </div>
                @endif

                <div class="min-w-0 flex-1">
                    <p class="font-bold">{{ $question->text }}</p>
                    <div class="mt-1 flex flex-wrap gap-1.5">
                        <span class="badge bg-stone-900/5 text-stone-600 tabular-nums dark:bg-white/10 dark:text-stone-300">#{{ $question->sort_order }}</span>
                        <span class="badge {{ $question->year ? 'bg-gold-300/40 text-[#6b4d10] dark:bg-gold-400/20 dark:text-gold-300' : 'bg-pine-100 text-pine-800 dark:bg-pine-800 dark:text-pine-100' }}">
                            {{ $question->year ?? 'Every year' }}
                        </span>
                        @if($question->hasOptions())
                            <span class="badge rounded-lg bg-sky-100 text-sky-800 dark:bg-sky-900/50 dark:text-sky-200">Pick one: {{ implode(', ', $question->options) }}</span>
                        @endif
                        @unless($question->is_active)
                            <span class="badge bg-berry-100 text-berry-800 dark:bg-berry-900/60 dark:text-berry-100">Off</span>
                        @endunless
                        <span class="badge text-stone-600 tabular-nums dark:text-stone-300">{{ $question->answers_count }} {{ \Illuminate\Support\Str::plural('answer', $question->answers_count) }}</span>
                    </div>
                </div>

                <div class="flex w-full justify-end gap-2 sm:w-auto">
                <a class="btn btn-ghost px-3 py-1.5 text-sm" href="{{ route('admin.questions.edit', $question) }}">Edit</a>
                <form action="{{ route('admin.questions.toggle', $question) }}" method="POST">
                    @csrf
                    <button class="btn btn-ghost px-3 py-1.5 text-sm whitespace-nowrap" type="submit">{{ $question->is_active ? 'Switch off' : 'Switch on' }}</button>
                </form>
                </div>
            </li>
        @endforeach
    </ol>
@endif
