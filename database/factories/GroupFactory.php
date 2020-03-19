<?php

use Faker\Generator as Faker;

$factory->define(App\Group::class, function (Faker $faker) {
	static $counter = 1;

    return [
        'group_phase_id' => function () {
            return factory('App\GroupPhase')->create()->id;
        },
        'name' => 'Gruppe ' . $counter++
    ];
});
