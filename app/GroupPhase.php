<?php

namespace App;

use App\Services\GroupElimination;

class GroupPhase extends Phase
{
	public function phase()
    {
        return $this->morphOne(Phase::class, 'phaseable');
    }

    public function reset()
    {
        $this->groups->each->delete();
        $this->availables->each->delete();
        $this->phase->championship->participants()->available()->delete();
        $this->phase->participants()->detach();

        if ($nextPhaseable = optional($this->phase->nextPhase)->phaseable)
        {
            $nextPhaseable->reset();
        }
    }

    public function groups()
    {
    	return $this->hasMany(Group::class, 'group_phase_id');
    }

    public function matches()
    {
        return $this->hasManyThrough(Match::class, Group::class, 'group_phase_id', 'matchable_id')->where('matchable_type', 'App\Group');
    }

    public function standings()
    {
        return $this->hasManyThrough(Standing::class, Group::class, 'group_phase_id', 'group_id');
    }

    public function allMatchesFinished()
    {
        return $this->matches->filter(function($match)
        {
            return !$match->isFinished();
        })
        ->count() == 0;
    }

    public function availables()
    {
        return $this->hasMany(Available::class, 'group_phase_id');
    }

    public function qualified()
    {
        return $this->belongsTo(Qualified::class);
    }

    public function extraQualified()
    {
        return $this->belongsTo(Qualified::class, 'extra_qualified_id');
    }

    public function qualifieds()
    {
        // dd($this->phase->nextPhase);
        return $this->phase->nextPhase ? Qualified::where('name', '<>', 'Gruppensieger')->get() : Qualified::all();
    }

    public function generateDraw($request)
    {
        $this->reset();

        $this->update([
            'group_size' => $request->group_size,
            'qualified_id' => $request->qualified_id,
            'extra_qualified_id' => $request->extra_qualified_id
        ]);

         $participants = $this->phase->championship->getAvailableParticipants($this->phase);

        (new GroupElimination($this->phase, $participants))->build();
    }

    public function proceedMatch($match)
    {
        $winner = $match->getWinner();
        $loser = $match->getLoser();
        $result = $match->result;

        $match->p1->standing->sets_won += $result->left;
        $match->p1->standing->sets_lost += $result->right;

        $match->p2->standing->sets_won += $result->right;
        $match->p2->standing->sets_lost += $result->left;

        foreach ($match->sets as $set)
        {
            if ($set->points)
            {
                $match->p1->standing->points_won += $set->leftPoints();
                $match->p1->standing->points_lost += $set->rightPoints();
                $match->p2->standing->points_won += $set->rightPoints();
                $match->p2->standing->points_lost += $set->leftPoints();
            }
        }

        $winner->standing->wins++;
        $loser->standing->defeats++;

        $winner->standing->save();
        $loser->standing->save();

        if ($this->allMatchesFinished())
        {
            $this->phase->finish();

            if ($this->phase->nextPhase->isDrawn())
            {
                logger('replaceAvailables');
                $this->replaceAvailables();
            }
        }
    }

    public function replaceAvailable($availables, $standings, $isBestOfRest, $removed = null)
    {
        $nextPhase = $this->phase->nextPhase;

        $availables->each(function($a) use ($removed, $standings, $nextPhase, $isBestOfRest)
        {
            // Wenn Teilnehmer regulär qualifiziert ist
            if (! $isBestOfRest)
            {
                $standing = $standings->filter(function($s) use ($a)
                {
                    return $s->group->id == $a->participantable->group->id;
                })
                ->values()[$a->participantable->position - 1];
            }
            else
            {
                $standing = $standings->values()[$a->participantable->position - 1];
            }

            // Weise den Teilnehmer der Phase zu (participant_phase)
            $nextPhase->participants()->attach($standing->participant);

            collect(['p1', 'p2'])->each(function($p) use ($a, $standing, $nextPhase)
            {
                $nextPhase->koPhase()->matches()->where("{$p}_id", $a->id)->get()->map(function($match) use ($p, $standing)
                {
                    $match->$p()->associate($standing->participant)->save();
                });
            });

            if (!is_null($removed)) $removed->push($standing);
        });

        return $removed;
    }

    public function replaceAvailables()
    {
        $standings = $this->standings;
        $participants = $this->phase->nextPhase->participants()->available()->get();

        if ($participants->count() == 0)
        {
            $participants = $this->phase->championship->participants()->available()->get();
        }

        list($qualified, $rest) = $participants->partition(function($p)
        {
            return $p->participantable->best == false;
        });

        // Entferne alle Teilnehmer der Phase (participant_phase)
        $this->phase->nextPhase->participants()->detach($participants->pluck('id')->all());

        $toBeRemoved = $this->replaceAvailable($qualified, $standings, false, collect());

        $filtered = $standings->filter(function($standing) use ($toBeRemoved)
        {
            return !$toBeRemoved->contains('id', $standing->id);
        });

        $this->replaceAvailable($rest, $filtered, true);
    }

    public function createAvailables()
    {
        $seed = 1;

        for ($i = 1; $i <= $this->qualified->value; $i++)
        {
            foreach ($this->groups as $j => $group)
            {
                $available = $this->availables()->create([
                    'group_id' => $group->id,
                    'position' => $i,
                    'seed' => $seed++
                ]);

                $this->phase->championship->addParticipant($available, true);
            }
        }

        for ($i = 1; $i <= optional($this->extraQualified)->value; $i++)
        {
            $available = $this->availables()->create([
                'seed' => $seed++,
                'best' => true,
                'position' => $i
            ]);

            $this->phase->championship->addParticipant($available, true);
        }
    }
}
