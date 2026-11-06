@php
    $recipient = $secretSanta->to;
    $profile = $recipient->profile();
@endphp
@component('mail::message')
# Secret Santa has begun!

Your secret santa is: **{{ $recipient->name }}**

@if($profile->isNotEmpty())
**Here's what {{ $recipient->name }} shared:**

@component('mail::panel')
@foreach($profile as $item)
**{{ $item['question']->text }}**<br>
{!! nl2br(e($item['answer'])) !!}

@endforeach
@endcomponent
@endif

@if($recipient->wishlist)

**{{ $recipient->name }}'s wishlist:**

@component('mail::panel')
{{ $recipient->wishlist }}
@endcomponent

@endif

@if($profile->isEmpty() && blank($recipient->wishlist))
{{ $recipient->name }} hasn't answered their questionnaire yet. Check your page later, as their answers will appear there.
@endif

@component('mail::button', ['url' => route('participant.show', $secretSanta->from), 'color' => 'success'])
See {{ $recipient->name }}'s latest answers
@endcomponent

*Have fun!*
@endcomponent
