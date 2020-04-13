<?php

use Faker\Generator as Faker;

$autoIncrement = autoIncrement();

$factory->define(App\Group::class, function (Faker $faker) use ($autoIncrement) {
	$autoIncrement->next();

    return [
        'group_phase_id' => function () {
            return factory('App\GroupPhase')->create()->id;
        },
        'name' => 'Gruppe ' . $autoIncrement->current(),
        'order' => $autoIncrement->current()
    ];
});

function autoIncrement()
{
    for ($i = 0; $i < 1000; $i++)
    {
        yield $i;
    }
}
