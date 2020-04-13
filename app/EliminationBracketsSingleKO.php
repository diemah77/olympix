<?php

namespace App;

class EliminationBracketsSingleKO extends EliminationBrackets
{
	public function build()
	{
		$this->createRounds();
		$this->fillFirstRound();
        $this->fillWinnersSide();
		$this->proceedByeMatches($this->firstRound);
	}

	public function fillWinnersSide()
	{
        $nextRound = $this->firstRound->nextRound;
        $matchCount = $this->firstRound->matches()->count();

        while ($nextRound != null):
            $previousRound = $nextRound->previousRound;
            $matches = $previousRound->matches()->orderBy('order')->get();

            if ($previousRound->isSemi() && $this->phase->championship->third_place)
            {
                $losersMatch = $previousRound->nextLosersRound->matches()->create([
                    'name' => $matches->first()->name,
                    'order' => $matchCount + 1,
                    'set_count' => $this->phase->championship->sets,
                    'championship_id' => $this->phase->championship->id,
                    'tournament_id' => $this->phase->championship->tournament->id,
                ]);
            }

			for ($i = 0; $i < $matches->count(); $i++)
			{
                $matchCount++;

				$winnerMatch = $nextRound->matches()->create([
					'name' => $matches->get($i)->name,
					'order' => $matchCount,
					'set_count' => $this->phase->championship->sets,
					'championship_id' => $this->phase->championship->id,
					'tournament_id' => $this->phase->championship->tournament->id,
				]);

				$matches->get($i)->nextMatch()->associate($winnerMatch)->save();

                if ($matches->count() > 1)
                {
                    $matches->get($i + 1)->nextMatch()->associate($winnerMatch)->save();
                }

                if (isset($losersMatch))
                {
                    $matches->get($i)->nextLosersMatch()->associate($losersMatch)->save();
                    $matches->get($i + 1)->nextLosersMatch()->associate($losersMatch)->save();
                }

				$i++;
			}

			$nextRound = $nextRound->nextRound;
		endwhile;
	}

	public function createRounds()
	{
		$lastRound = $this->firstRound;

		for ($i = 1; $i <= $this->roundCount() - 1; $i++)
		{
            if ($lastRound->isSemi() && $this->phase->championship->third_place)
            {
                $nextLosersRound = $this->phase->phaseable->rounds()->create([
                    'name' => 'Spiel um Platz 3',
                    'side' => $this->round::SIDE_WINNER,
                    'order' => $i
                ]);

                $lastRound->nextLosersRound()->associate($nextLosersRound)->save();
            }

			$nextRound = $this->phase->phaseable->rounds()->create([
				'type' => $this->bracketSize / pow(2, $i),
                'side' => $this->round::SIDE_WINNER,
                'order' => $i + 1
			]);

			$lastRound->nextRound()->associate($nextRound)->save();
			$lastRound = $nextRound;
		}
	}
}
