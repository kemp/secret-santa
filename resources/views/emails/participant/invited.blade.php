@component('mail::message')
# You're invited!

You've been invited to participate in a Secret Santa with the following people:

@foreach($party->participants as $partyParticipant)
- {{ $partyParticipant->name }}
@endforeach

@component('mail::button', ['url' => route('participant.show', $participant)])
Join Secret Santa
@endcomponent

@endcomponent
