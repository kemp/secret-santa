<form action="{{ route('party.deadline.update', ['party' => $party, 'edit_token' => request()->edit_token]) }}" method="POST" class="space-y-2">
    @csrf
    @method('PATCH')

    <label class="label" for="party_deadline">Questionnaire deadline</label>
    <div class="flex gap-2">
        <input class="field min-w-0 flex-1" type="date" name="deadline" id="party_deadline" value="{{ $party->deadline?->toDateString() }}">
        <button class="btn btn-ghost" type="submit">Save</button>
    </div>
    <p class="hint">Answers can be edited until the end of this day. Clear it for no deadline.</p>
</form>
