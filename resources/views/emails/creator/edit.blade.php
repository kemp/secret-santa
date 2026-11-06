@component('mail::message')
# Secret Santa Created

You've successfully created a new Secret Santa. Here's the link to send to everyone:

<{{ route('party.show', $party) }}>

And this is the link that you should keep private, because it allows you to start the secret santa and reveal everyone's secret santa:

@component('mail::button', ['url' => route('party.show', ['party' => $party, 'edit_token' => $participant->edit_token])])
Manage the party & draw names
@endcomponent

Don't forget to fill in your own questionnaire, so your Secret Santa knows what to get you:

<{{ route('participant.show', $participant) }}>

@endcomponent
