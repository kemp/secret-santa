@component('mail::message')
# You're in, {{ $participant->name }}!

You've joined **{{ $participant->party->creator()->name }}'s Secret Santa**. This is your private link. Use it to answer a few quick questions for your Secret Santa, and after the draw, to see who you're buying for.

@component('mail::button', ['url' => route('participant.show', $participant), 'color' => 'success'])
Open my Secret Santa page
@endcomponent

@if($participant->party->deadline)
You can change your answers until **{{ $participant->party->deadline->format('l, F j') }}**.
@endif

Keep this email. The link is just for you, so please don't forward it.

*Happy holidays!*
@endcomponent
