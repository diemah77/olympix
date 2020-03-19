<?php

use Faker\Generator as Faker;

$factory->define(App\Player::class, function (Faker $faker) {
    return [
        'firstname' => $faker->firstname,
        'lastname' => $faker->lastname,
        'ttr' => $faker->numberBetween(1300, 1800),
        'tournament_id' => function () {
            return factory('App\Tournament')->create()->id;
        }
    ];
});
