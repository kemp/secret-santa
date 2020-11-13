@component('mail::message')
# Secret Santa Created

You've successfully created a new Secret Santa. Here's the link to send to everyone:

<{{ route('party.show', $party) }}>

And this is the link that you should keep private, because it allows you to start the secret santa and reveal everyone's secret santa:

<{{ route('party.show', ['party' => $party, 'edit_token' => $participant->edit_token]) }}>

@endcomponent
