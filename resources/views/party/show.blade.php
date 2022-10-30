@extends('layouts.app')

@section('title', "{$party->creator()->name} has invited you to join Secret Santa!")

@section('main')
    <h2>{{ $party->creator()->name }} has invited you to join Secret Santa!</h2>

    <div class="mb-4">
        <label for="invitation_link">Share this link with others:</label>
        <div class="w-full flex">
            <input
                type="text"
                id="invitation_link"
                class="border border-gray-300 bg-gray-100 border rounded flex-grow mr-1"
                readonly
                onfocus="this.select() && this.setSelectionRange(0, 99999)"
                value="{{ route('party.show', $party) }}"
            >
            <button
                type="button"
                class="border border-gray-500 rounded px-2"
                onclick="copyInvitationLink()"
            >Copy</button>
        </div>
    </div>

    <p>Participants:</p>

    <ul class=" mb-4 list-disc list-inside">
        @foreach($party->participants as $participant)
            <li>{{ $participant->name }}</li>
        @endforeach
    </ul>

    <hr class="mb-4">

    @if($party->began_at)
        <h2 class="text-center text-2xl">The Secret Santa has begun!</h2>
    @else
        @if($party->canBeInitiated())
            <form action="{{ route('party.initiate', ['party' => $party, 'edit_token' => request()->edit_token]) }}" method="POST">
                @csrf

                <input
                    type="submit"
                    class="mb-4 w-full rounded bg-teal-100 border border-teal-600 py-2 px-4 font-bold cursor-pointer"
                    value="Start Secret Santa"
                >
            </form>
        @endif

        <p class="font-semibold">Want to join?</p>

        @include('participant-form', ['route' => route('party.participants.store', $party), 'submitText' => 'Join Secret Santa'])
    @endif

@endsection

@push('scripts')
    <script>
        function copyInvitationLink() {
            /* Get the text field */
            var copyText = document.getElementById("invitation_link");

            /* Select the text field */
            copyText.select();
            copyText.setSelectionRange(0, 99999); /*For mobile devices*/

            /* Copy the text inside the text field */
            document.execCommand("copy");

            /* Alert the copied text */
            alert('Invitation link copied!');
        }
    </script>
@endpush
