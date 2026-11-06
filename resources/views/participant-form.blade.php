@include('errors', ['errors' => $errors])

<form action="{{ $route }}" method="POST" data-save="add-participant" class="space-y-4">
    @csrf

    <div>
        <label class="label" for="name">Your name</label>
        <input
            class="field @error('name') field-invalid @enderror"
            type="text"
            name="name"
            id="name"
            placeholder="e.g. Aunt Linda"
            autocomplete="name"
            required
            value="{{ old('name') }}"
        >
    </div>

    <div>
        <label class="label" for="email">Your email</label>
        <input
            class="field @error('email') field-invalid @enderror"
            type="email"
            name="email"
            id="email"
            placeholder="you@example.com"
            autocomplete="email"
            inputmode="email"
            required
            value="{{ old('email') }}"
        >
        <p class="hint mt-1.5">We'll email you a private link to your questionnaire and, later, who you're buying for.</p>
    </div>

    @isset($suggestedDeadline)
        <div>
            <label class="label" for="deadline">Questionnaire deadline <span class="font-normal text-stone-600 dark:text-stone-300">(optional)</span></label>
            <input
                class="field @error('deadline') field-invalid @enderror"
                type="date"
                name="deadline"
                id="deadline"
                min="{{ now()->addDay()->toDateString() }}"
                value="{{ old('deadline', $suggestedDeadline->toDateString()) }}"
            >
            <p class="hint mt-1.5">Everyone can edit their answers until the end of this day. Leave it empty for no deadline.</p>
        </div>
    @endisset

    <button class="btn btn-primary w-full py-3 text-base" type="submit">
        {{ $submitText }}
    </button>
</form>
