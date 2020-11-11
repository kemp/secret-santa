<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Participant;
use Faker\Generator as Faker;

$factory->define(Participant::class, function (Faker $faker) {
    return [
        'party_id' => factory(\App\Party::class)->create()->id,
        'to_name' => $faker->name,
        'to_email' => $faker->email,
        'sent_at' => now(),
        'wishlist' => $faker->paragraph,
        'confirmed_at' => now(),
    ];
});
