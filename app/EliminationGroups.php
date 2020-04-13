<?php

namespace App\Services;

use App\Match;
use App\Group;
use App\Standing;

class GroupElimination
{
	public $phase;
	public $participants;
	public $groups;
	public $groupCount;

	public function __construct($phase, $participants)
	{
		$this->phase = $phase;
		$this->participants = $participants;

		if ($participants->count() < 2)
		{
			App::abort(505, 'Zu wenige Teilnehmer');
		}
	}

	public function build()
	{
		$this->calcGroupCount();
		$this->createGroups();
		$this->fillParticipants();
		$this->createMatches();
		$this->createAvailables();
	}

	private function fillParticipants()
	{
		// Bei zufälliger Auslosung die Teilnehmer durchwürfeln
		$participants = $this->phase->isDrawRandom() ? $this->participants->shuffle() : $this->participants;

		// Teilnehmer der Phase zuweisen
		$this->phase->participants()->attach($participants);

		// Teilnehmer in Pools aufteilen
		$pools = $participants->split($this->calcPoolSize());

		foreach ($pools as $j => $pool)
		{
			$participants = $j % 2 == 1 ? $pool->reverse() : $pool;

			// values() setzt die Indezes zurück (0, 1, 2 ...)
			foreach ($participants->values() as $i => $participant)
			{
				$standing = Standing::create([
					'group_id' => $this->groups->get($i)->id,
				]);

				$standing->participant()->associate($participant)->save();
			}
		}
	}

	public function createMatches()
	{
        $groupsCount = $this->groups->count();

		foreach ($this->groups as $i => $group)
		{
			$standings = $group->standings()->orderBy('participant_id', 'asc')->get();
            $rivals = $standings;

            $matchCount = $this->roundMatchCount() - $group->roundMatchCount();

			foreach ($standings as $j => $a)
			{
				$rivals->shift();

				foreach ($rivals->shuffle() as $k => $b)
				{
					$match = $group->matches()->create([
						'set_count' => $this->phase->championship->sets,
						'championship_id' => $this->phase->championship->id,
                        'tournament_id' => $this->phase->championship->tournament->id,
                        'order' => $i + $k * $matchCount * 10 + 1 + $j * $groupsCount
					]);

					$match->p1()->associate($a->participant)->save();
					$match->p2()->associate($b->participant)->save();
				}
			}
		}
    }

    public function roundMatchCount()
    {
        return $this->groups->reduce(function($count, $group)
        {
            return $count + $group->roundMatchCount();
        });
    }

	public function createAvailables()
	{
		$this->phase->groupPhase()->createAvailables();
	}

	private function calcPoolSize()
	{
		return ceil($this->participants->count() / $this->groupCount);
	}

	private function createGroups()
	{
        $this->groups = factory('App\Group', $this->groupCount)->create([
            'group_phase_id' => $this->phase->groupPhase()->id
        ]);
	}

	private function calcGroupCount()
	{
		$this->groupCount = max(intval($this->participants->count() / $this->phase->groupPhase()->group_size), 1);
	}
}
