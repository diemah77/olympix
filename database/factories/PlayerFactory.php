<?php

use Faker\Generator as Faker;

$autoIncrement = autoIncrement();

$factory->define(App\Player::class, function (Faker $faker) use ($autoIncrement) {
    $autoIncrement->next();

    return [
        'firstname' => sprintf("%02d", $autoIncrement->current()),
        'lastname' => 'T' . $autoIncrement->current(),
        'ttr' => 800 + $autoIncrement->current(),
        'tournament_id' => function () {
            return factory('App\Tournament')->create()->id;
        }
    ];
});
