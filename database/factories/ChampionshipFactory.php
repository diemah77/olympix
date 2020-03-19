<?php

use Faker\Generator as Faker;

$factory->define(App\Championship::class, function (Faker $faker) {
    return [
        'tournament_id' => function () {
            return factory(App\Tournament::class)->create()->id;
        },
        'name' => 'Einzel',
        'system_id' => 3,
        'type_id' => 1,
        'sets' => 5
    ];
});
