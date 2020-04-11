<?php

namespace App;

use App;
use Illuminate\Support\Collection;

abstract class EliminationBrackets
{
	public $phase;
	public $championship;
	public $koPhase;
	public $round;
	public $bracketSize;
	public $participants;
	public $firstRound;

	public function __construct($phase, $participants)
	{
		$this->phase = $phase;
		$this->koPhase = $this->phase->koPhase();
        $this->participants = $participants;

		$this->round = new Round();

		if ($participants->count() < 2)
		{
			App::abort(505, 'Zu wenige Teilnehmer');
		}

		$this->calculateBracketSize();
		$this->firstRound = $this->koPhase->rounds()->create([
			'type' => $this->bracketSize,
			'side' => $this->round::SIDE_WINNER
		]);
	}

	abstract protected function build();

	public function fillFirstRound()
	{
        $all = $this->seededParticipants();

		// Teilnehmer der Phase zuweisen
		$this->phase->participants()->attach($all);

		$matches = collect();
		for ($i = 1; $i <= $this->bracketSize / 2; $i++)
		{
			$first = $all->shift();
			$second = $all->pop();

			$match = new Match([
				'name' => $i,
				'order' => $i,
				'set_count' => $this->phase->championship->sets,
				'championship_id' => $this->phase->championship->id,
				'tournament_id' => $this->phase->championship->tournament->id,
			]);

			$match->p1()->associate($first);
			$match->p2()->associate($second);

			$matches->push($match);
		}

		$this->firstRound->matches()->saveMany($matches->all());
    }

    protected function seededParticipants() : Collection
    {
        list($seeded, $unseeded) = $this->participants->partition(function ($p)
        {
            return $p->seed > 0;
        });

        $all = $seeded->sortBy('seed');

        if ($this->phase->isDrawRandom())
        {
            $unseeded = $unseeded->shuffle();
        }

        $all = $all->concat($unseeded);

        $byes = $this->getByeParticipants($this->bracketSize - $all->count());

        $all = $all->concat($byes)->values()->map(function ($p, $index)
        {
            $p->seed = $this->seedPlayer($index + 1, $this->bracketSize) + 1;

            return $p;
        })
        ->sortBy('seed');

        return $all;
    }

    public function seedPlayer($rank, $bracketSize)
    {
        // base case, if rank == 1, return position 0
        if ($rank <= 1)
        {
            return 0;
        }

        // if our rank is even we need to put the player into the right part
        // so we add half the part size to his position
        // and make a recursive call with half the rank and half the part size
        if ($rank % 2 == 0)
        {
            return $bracketSize / 2 + $this->seedPlayer($rank / 2, $bracketSize / 2);
        }

        // if the rank is uneven, we put the player in the left part
        // since rank is uneven we need to add + 1 so that it stays uneven
        return $this->seedPlayer($rank / 2 + 1, $bracketSize / 2);
    }

	public function calculateBracketSize()
	{
		$this->bracketSize = pow(2, $this->roundCount());
	}

	public function roundCount()
	{
		return ceil(log($this->participants->count()) / log(2));
	}

	public function getByeParticipants($byeCount)
	{
		$byes = collect();

		for ($i = 0; $i < $byeCount; $i++)
		{
			$byes->push($this->phase->championship->getByeParticipant());
		}

		return $byes;
	}

	public function proceedByeMatches($round)
	{
		foreach($round->matches as $match)
		{
			$this->koPhase->proceedByeMatch($match);
		}
	}
}
