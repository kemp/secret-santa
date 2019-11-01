<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\SecretSanta;
use Faker\Generator as Faker;

$factory->define(SecretSanta::class, function (Faker $faker) {
    $party_id = factory(App\Party::class)->create();
    $participants = factory(App\Participant::class, 2)->create(['party_id' => $party_id]);
    
    return [
        'party_id' => $party_id,
        'from_id' => $participants[0]->id,
        'to_id' => $participants[1]->id,
    ];
});
