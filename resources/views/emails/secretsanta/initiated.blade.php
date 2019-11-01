@component('mail::message')
# Secret Santa has begun!

Your secret santa is: **{{ $secretSanta->to->name }}**

@if($secretSanta->to->wishlist)

**{{ $secretSanta->to->name }}'s wishlist:**

@component('mail::panel')
{{ $secretSanta->to->wishlist }}
@endcomponent

@endif

*Have fun!*
@endcomponent
