<?php

namespace App;

class DoublesDraw
{
	public static function generate($championship, $mode)
	{
        $registrations = $championship->registrations->load('player');

        if ($registrations->count() < 2)
        {
            throw new \Exception('Zu wenige Spieler!');
        }

		if ($registrations->count() % 2 == 1)
		{
            $registrations = $registrations->shuffle();
            $registrations->shift();
		}

        $registrations = $registrations->map(function($r)
        {
            $r->ttr = $r->player->ttr;

            return $r;
        })
        ->sortByDesc('ttr')
        ->values();

		$mode = $mode ?? Championship::DOUBLES_DRAW_RANDOM;

		$doubles = collect([]);
		$noOfDoubles = $registrations->count() / 2;

		if ($mode == Championship::DOUBLES_DRAW_RANDOM)
		{
			$registrations = $registrations->shuffle();

			for ($i = 1; $i <= $noOfDoubles; $i++)
			{
				$doubles->push($registrations->splice(0, 2));
			}
		}
		elseif ($mode == Championship::DOUBLES_DRAW_TTR || $mode == Championship::DOUBLES_DRAW_POOLS)
		{
			$pools = $registrations->chunk($noOfDoubles);
			$pool1 = $pools->first();
			$pool2 = $mode == Championship::DOUBLES_DRAW_TTR ? $pools->last() : $pools->last()->shuffle();

			for ($i = 1; $i <= $noOfDoubles; $i++)
			{
				$double = collect();
				$double->push($pool1->shift());
				$double->push($pool2->pop());

				$doubles->push($double);
			}
		}

		return $doubles;
	}

}
