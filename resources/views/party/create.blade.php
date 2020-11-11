@extends('layouts.app')

@section('main')
    <h2>Create a new Secret Santa</h2>

    <p>How it works:</p>

    <ol class="mb-4 list-decimal list-outside ml-4">
        <li>Invite the participants to the secret santa</li>
        <li>Everyone confirms their participation and adds items to their wishlist (optional)</li>
        <li>Secret Santa assignments are sent to every participant</li>
    </ol>

    <hr>

    <p class="my-4">Please enter the name and email address of the participants below.</p>

    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('party.store') }}" method="POST">
        <div id="participant-fields" class="my-5">
            <div class="participant-field">
                <div>Yourself: </div>
                <input 
                    class="name placeholder-gray-800" 
                    type="text" 
                    name="names[]" 
                    placeholder="Your Name"
                    autocomplete="name"
                >
                <input 
                    class="email placeholder-gray-800" 
                    type="email" 
                    name="emails[]" 
                    placeholder="Your Email"
                    autocomplete="email"
                >
            </div>
            <div class="participant-field">
                <div>Another participant: </div>
                <input 
                    class="name placeholder-gray-800" 
                    type="text" 
                    name="names[]" 
                    placeholder="Their Name"
                    autocomplete="off"
                >
                <input 
                    class="email placeholder-gray-800" 
                    type="email" 
                    name="emails[]" 
                    placeholder="Their Email"
                    autocomplete="off"
                >
            </div>
       </div>

        <div class="flex flex-col sm:flex-row">
            <button class="w-full rounded bg-teal-100 border border-teal-300 py-2 px-4 sm:mr-2 mb-4 sm:mb-0" type="button" onclick="addParticipant()"><span aria-hidden="true">&plus;</span> Add another participant</button>

            <input class="w-full rounded bg-teal-300 border border-teal-600 py-2 px-4 font-bold cursor-pointer" type="submit" value="Start Secret Santa 🛷">
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        ((window, document) => {
            'use strict';

            function addParticipant() {
                let html = `
                    <div>Another participant:</div>
                    <button type="button" class="rounded bg-red-200 m-2 py-1 px-2 border border-red-800 text-sm text-red-800" onclick="this.parentElement.remove()"><span aria-hidden="true">&times;</span> Remove</button>
                    <input class="name placeholder-gray-800" type="text" name="names[]" placeholder="Their Name" autocomplete="off">
                    <input class="email placeholder-gray-800" type="email" name="emails[]" placeholder="Their Email" autocomplete="off">
                `;

                let participantFields = document.getElementById('participant-fields');

                let participantField = document.createElement('div');
                participantField.classList.add('participant-field');
                participantField.innerHTML = html;

                participantFields.appendChild(participantField);

                participantField.querySelector('.name').focus();
            }

            window.addParticipant = addParticipant;
        })(window, document);
    </script>
@endpush
