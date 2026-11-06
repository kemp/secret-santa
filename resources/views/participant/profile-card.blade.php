{{--
    A recipient's answers, shown to their Secret Santa.
    @var $person \App\Models\Participant
    @var $profile \Illuminate\Support\Collection<int, array{question: \App\Models\Question, answer: string}>
--}}
<article class="card overflow-hidden p-0 sm:p-0">
    <header class="flex items-center gap-4 bg-pine-800 px-6 py-5 text-white sm:px-8 dark:bg-pine-800">
        @include('avatar', ['name' => $person->name, 'class' => 'size-12 text-lg ring-pine-700'])
        <div>
            <h3 class="text-xl text-white">{{ $person->name }}</h3>
            <p class="text-sm text-pine-100">What they'd like their Secret Santa to know</p>
        </div>
    </header>

    @if($profile->isEmpty() && blank($person->wishlist))
        <div class="px-6 py-10 text-center sm:px-8">
            <span class="flex justify-center text-pine-600 dark:text-pine-300">@include('icon', ['name' => 'tree', 'class' => 'size-9'])</span>
            <p class="mt-3 font-bold">{{ $person->name }} hasn't answered any questions yet.</p>
            <p class="hint mx-auto mt-1 max-w-sm">Their answers will show up here as soon as they fill them in. Until then, a little detective work never hurt anyone.</p>
        </div>
    @else
        <dl class="divide-y divide-stone-200 px-6 sm:px-8 dark:divide-white/10">
            @foreach($profile as $item)
                <div class="py-4">
                    <dt class="text-sm text-stone-600 dark:text-stone-300">{{ $item['question']->text }}</dt>
                    <dd class="mt-1 text-stone-900 dark:text-stone-50">
                        @if($item['question']->hasOptions())
                            <span class="badge bg-berry-100 px-3 py-1 text-sm text-berry-800 dark:bg-berry-900/60 dark:text-berry-100">{{ $item['answer'] }}</span>
                        @else
                            <span class="whitespace-pre-line">{{ $item['answer'] }}</span>
                        @endif
                    </dd>
                </div>
            @endforeach

            @if(filled($person->wishlist))
                <div class="py-4">
                    <dt class="text-sm text-stone-600 dark:text-stone-300">Wishlist</dt>
                    <dd class="mt-1 whitespace-pre-line text-stone-900 dark:text-stone-50">{{ $person->wishlist }}</dd>
                </div>
            @endif
        </dl>
    @endif
</article>
