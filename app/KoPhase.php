<?php

namespace App;

class KoPhase extends Phase
{
	public function phase()
    {
        return $this->morphOne(Phase::class, 'phaseable');
    }

    public function rounds()
    {
    	return $this->hasMany(Round::class, 'ko_phase_id');
    }

    public function matches()
    {
        return $this->hasManyThrough(Match::class, Round::class, 'ko_phase_id', 'matchable_id')->where('matchable_type', 'App\Round');
    }

    public function reset()
    {
        $this->rounds->each->delete();

        // Lösche Freilose
        $this->phase->participants()->bye()->delete();

        // Setze die Anzahl Niederlagen zurück
        $this->phase->participants()->update(['defeats' => 0]);

        // Entferne alle Teilnehmer der Phase
        $this->phase->participants()->detach();
    }

    public function generateDraw($request)
    {
        $this->reset();

        $participants = $this->phase->championship->getAvailableParticipants($this->phase);

        if ($this->phase->championship->isDoubleElimination())
        {
            $elimination = (new EliminationBracketsDoubleKO($this->phase, $participants));
        }
        else
        {
            $elimination = (new EliminationBracketsSingleKO($this->phase, $participants));
        }

        $elimination->build();
    }

    public function proceedMatch($match)
    {
        // Sieger und Verlierer ermitteln
        $winner = $match->getWinner();
        $loser = $match->getLoser();
        $loser->increment('defeats');

        $nextMatchForWinner = $match->nextMatch;

        if ($this->phase->championship->isDoubleElimination())
        {
            $nextMatchForLoser = $match->matchable->isWinner() ? $match->nextLosersMatch : $nextMatchForWinner;

            logger($match);
            logger($nextMatchForLoser);
            logger($nextMatchForWinner);

            if ($match->matchable->name == 'Finale 2')
            {
                if ($winner->defeats > 0)
                {
                    $finalRound = $this->createFinalRound($match);
                    $finalMatch = $this->createFinalMatch($match, $finalRound);

                    $nextMatchForWinner = $finalMatch;
                    $nextMatchForLoser = $finalMatch;
                }
            }

            if (! $loser->isSuspended())
            {
                $advancedMatch = $this->advanceParticipant($nextMatchForLoser, $loser);
                $this->proceedByeMatch($advancedMatch, $loser);
            }
        }

        $this->advanceParticipant($nextMatchForWinner, $winner);

        if ($this->phase->championship->createThirdPlaceGame($match->matchable))
        {
            $this->advanceParticipant($match->nextLosersMatch, $loser);
        }
    }

    public function createFinalRound($match)
    {
        $finalRound = $this->rounds()->create([
            'name' => 'Finale 3',
            'side' => $match->matchable::SIDE_WINNER
        ]);

        $match->matchable->nextRound()->associate($finalRound)->save();

        return $finalRound;
    }

    public function createFinalMatch($match, $round)
    {
        $finalMatch = $round->matches()->create([
            'name' => 1,
            'order' => 1,
            'set_count' => $this->phase->championship->sets,
            'championship_id' => $this->phase->championship->id,
			'tournament_id' => $this->phase->championship->tournament->id,
        ]);

        $match->nextMatch()->associate($finalMatch)->save();

        return $finalMatch;
    }

    public function proceedByeMatch($match)
    {
        if ($match->hasByes() && !$match->isEmpty())
        {
            $winner = $match->isBye($match->p1) ? $match->p2 : $match->p1;
            $nextMatchForWinner = $match->nextMatch;
            $this->advanceParticipant($nextMatchForWinner, $winner);

            if ($this->phase->championship->isDoubleElimination())
            {
                $loser = $match->isBye($match->p1) ? $match->p1 : $match->p2;
                $loser->increment('defeats');
                $nextMatchForLoser = $match->matchable->isWinner() ? $match->nextLosersMatch : $nextMatchForWinner;
                $this->advanceParticipant($nextMatchForLoser, $loser);
            }

            $match->update([
                'status' => $match::$FINISHED,
                'winner' => $match->determineWinnerOfByeMatch()
            ]);
        }
    }

    public function advanceParticipant($match, $participant)
    {
        if (is_null($match))
        {
            return $match;
        }

        if ($participant->isSuspended())
        {
            return $match;
        }

        $key = is_null($match->p1) ? 'p1' : 'p2';

        if ($participant->isBye())
        {
            $key = is_null($match->p2) ? 'p2' : 'p1';
        }

        $match->$key()->associate($participant)->save();

        return $match;
    }
}
