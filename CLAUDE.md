@AGENTS.md

# Project notes

- No user accounts. Access is by unguessable tokens: `parties.invitation_token` (public invite),
  `participants.edit_token` (organiser, passed as `?edit_token=`), `participants.access_token`
  (each participant's private `/you/{token}` page). Never expose tokens in shared views or JSON.
- Do not change the draw/matching logic in `app/Services/SecretSantaRandomizer.php` or
  `PartyController::initiate()` without being asked.
- Questionnaire: `questions` (null `year` = every year) and `answers` (per participant, question, year).
  A party's questionnaire is `Question::forYear($party->year)`. Keep `participants.wishlist`; it's legacy data still shown to Santas.
- Views use `@extends('layouts.app')` + `@include` partials and the component classes in `resources/css/app.css`
  (`card`, `btn btn-primary`, `field`, `label`, `hint`, `alert-success`...). Run `vendor/bin/pint` and `php artisan test`.
