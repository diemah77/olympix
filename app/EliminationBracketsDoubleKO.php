<?php

namespace App;

class EliminationBracketsDoubleKO extends EliminationBrackets
{
	public $losersRounds;

	public function __construct($phase, $participants)
	{
		parent::__construct($phase, $participants);

		$this->losersRounds = collect();
	}

	public function build()
	{
		$this->createLosersRounds();
		$this->createRounds();
		$this->fillFirstRound();
		$this->fillLosersSide();
		$this->fillWinnersSide();
		$this->proceedByeMatches($this->firstRound);
		$this->proceedByeMatches($this->firstRound->nextLosersRound);
	}

	public function fillLosersSide()
	{
		$newRound = true;
        $nextRound = $this->firstRound->nextLosersRound;

		$matches = collect();
        while ($nextRound != null && $nextRound->name != 'Finale 2'):
            // $previousRound = $nextRound->name == 'Runde 1' ? $nextRound->previousLosersRound : $nextRound->previousRound;

			$matches = $nextRound->previousRound->matches()->orderBy('order')->get();

			for ($i = 0; $i < $matches->count(); $i++)
			{
				$m1 = $nextRound->matches()->create([
					'name' => $matches->get($i)->name,
					'order' => $i + 1,
					'set_count' => $this->phase->championship->sets,
					'championship_id' => $this->phase->championship->id,
					'tournament_id' => $this->phase->championship->tournament->id,
				]);

				$m2 = $m1;

				if (! $newRound && $matches->count() > 1)
				{
					$m2 = $nextRound->matches()->create([
						'name' => $matches->get($i)->name,
						'order' => $i + 2,
						'set_count' => $this->phase->championship->sets,
						'championship_id' => $this->phase->championship->id,
						'tournament_id' => $this->phase->championship->tournament->id,
					]);
				}

				$matches->get($i)->nextMatch()->associate($m1)->save();
				if ($matches->count() > 1) $matches->get($i + 1)->nextMatch()->associate($m2)->save();

				$i++;
			}

			$newRound = !$newRound;
			$nextRound = $nextRound->nextRound;
		endwhile;
	}

	/**
     * Ermittle das zugehörige Match der Verliererrunde für den Verlierer
     */
	protected function nextLosersMatchIndex($winnerMatchIndex, $roundIndex, $matchCount) : int
	{
        // In Runde 1 und im Finale bleiben alle Verlierer in ihren Hälften
        if ($roundIndex == 1 || $matchCount == 1)
        {
            return intval($winnerMatchIndex / 2);
        }

        // Ab Runde 2 wechseln Verlierer über Kreuz
        $offset = $winnerMatchIndex % 2 == 0 ? 1 : -1;

        return intval($winnerMatchIndex + $offset);

		// Erste und ab der 4. Runde
		$index = ceil($winnerMatchIndex / 2);

		// Zweite Runde
		// if ($roundIndex == 2)
		// {
		// 	$direction = $this->isTopHalf($winnerMatchIndex, $matchCount) ? 1 : -1;
		// 	$index = $winnerMatchIndex + ($matchCount * $direction / 2);
		// }

		// Dritte Runde
		// if ($roundIndex == 3)
		// {
		// 	$index = $matchCount / 2;

		// 	if (! $this->isTopHalf($winnerMatchIndex, $matchCount))
		// 	{
		// 		$index *= 3;
		// 	}

		// 	$index = $index - $winnerMatchIndex + 1;
		// }

		return intval($index);
	}

	private function isTopHalf($matchIndex, $matchCount)
	{
		return $matchIndex <= $matchCount / 2;
	}

	private function fillWinnersSide()
	{
		$nextRound = $this->firstRound->nextRound;
		$nextLosersRound = $this->firstRound->nextLosersRound;
		$j = 1;

		while ($nextRound != null):
			$matches = $nextRound->previousRound->matches()->orderBy('order')->get();

			for ($i = 0; $i < $matches->count(); $i++)
			{
				$winnerMatch = $nextRound->matches()->create([
					'name' => $matches->get($i)->name,
					'order' => $i + 1,
					'set_count' => $this->phase->championship->sets,
					'championship_id' => $this->phase->championship->id,
					'tournament_id' => $this->phase->championship->tournament->id,
				]);

				$matches->get($i)->nextMatch()->associate($winnerMatch)->save();
                if ($matches->count() > 1)
                {
                    $matches->get($i + 1)->nextMatch()->associate($winnerMatch)->save();
                }

				if (! is_null($nextRound->previousRound->type))
				{
                    $loserMatches = $nextRound->previousRound->nextLosersRound->matches;

					$index = $this->nextLosersMatchIndex($i, $j, $matches->count());
                    $loserMatch = $loserMatches->get($index);
					$matches->get($i)->nextLosersMatch()->associate($loserMatch)->save();

					if ($matches->count() > 1)
					{
                        $index = $this->nextLosersMatchIndex($i + 1, $j, $matches->count());
						$loserMatch = $loserMatches->get($index);
						$matches->get($i+1)->nextLosersMatch()->associate($loserMatch)->save();
					}
				}

				$i++;
			}

			$j++;

			if ($nextRound->name == 'Finale 2')
			{
				$lastLosersRound = $this->phase->phaseable->rounds()->whereSide(-1)->get()->last();
				$lastLosersRound->matches->first()->nextMatch()->associate($winnerMatch)->save();
			}

			$nextLosersRound = $nextRound->nextLosersRound;
			$nextRound = $nextRound->nextRound;
		endwhile;
	}

	private function createRounds()
	{
		$lastRound = $this->firstRound;

		for ($i = 1; $i <= $this->roundCount() - 1; $i++)
		{
			$nextRound = $this->phase->phaseable->rounds()->create([
				'type' => $this->bracketSize / pow(2, $i),
                'side' => $this->round::SIDE_WINNER,
                'order' => 3 * $i - 1
			]);

			$lastRound->nextRound()->associate($nextRound)->save();

			$index = $this->getAccordingLosersRound($i);
			$nextLosersRound = $this->losersRounds->get($index);
			$lastRound->nextLosersRound()->associate($nextLosersRound)->save();

			$lastRound = $nextRound;
		}

		// Finale 2 erstellen
		$nextRound = $this->phase->phaseable->rounds()->create([
			'name' => 'Finale 2',
            'side' => $this->round::SIDE_WINNER,
            'order' => 3 * $i - 2
		]);

		$lastRound->nextRound()->associate($nextRound)->save();

		// Verlierer des Finals in die Verliererseite setzen
		$index = $this->getAccordingLosersRound($i);
		$nextLosersRound = $this->losersRounds->get($index);
		$lastRound->nextLosersRound()->associate($nextLosersRound)->save();
	}

	private function createLosersRounds()
	{
		$lastRound = $this->firstRound;

		for ($i = 1; $i <= $this->losersRoundCount(); $i++)
		{
			$nextRound = $this->phase->phaseable->rounds()->create([
				'side' => $this->round::SIDE_LOSER,
				'number' => $i,
                'name' => "Runde {$i}",
                'order' => intval(3/2 * $i)
			]);

			$lastRound->nextRound()->associate($nextRound)->save();
			$lastRound = $nextRound;

			$this->losersRounds->push($nextRound);
		}
    }

	/**
	 * Liefert die entsprechende Verliererrunde. 1-basiert
	 * @param  int $roundNumber
	 * @return int
	 */
	private function getAccordingLosersRound($roundNumber)
	{
		if ($roundNumber == 1) return 0;

		return $roundNumber * 2 - 3;
	}

	private function losersRoundCount()
	{
		return ($this->roundCount() - 1) * 2;
	}
}
