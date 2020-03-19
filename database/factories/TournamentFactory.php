<?php

use Faker\Generator as Faker;

$factory->define(App\Tournament::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'tables_count' => $faker->numberBetween(3,5),
        'status' => 1
    ];
});
