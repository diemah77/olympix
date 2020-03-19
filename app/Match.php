<?php

namespace App;

use App\HasStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Match extends Model
{
    use HasStatus;

    protected $casts = [
        'winner' => 'integer',
        'status' => 'integer',
        'order' => 'integer',
        'result' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($match)
        {
            factory(Set::class, $match->championship->winningSets())->create(['match_id' => $match->id]);
        });

        static::updating(function ($match)
        {
            $match->winner = $match->determineWinner($match->result_id);

            // Cache::forget('c' . optional($match->championship)->id . 'relevant_matches');
        });

        static::deleting(function ($match)
        {
            // Cache::flush();
        });

        static::addGlobalScope('sorted', function (Builder $builder)
        {
            $builder->orderBy('matchable_type')->orderBy('order');
        });
    }

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

    public function sets()
    {
        return $this->hasMany(Set::class);
    }

    public function table()
    {
        return $this->belongsTo(Table::class);
    }

    public function result()
    {
        return $this->belongsTo(Result::class);
    }

    public function matchable()
    {
        return $this->morphTo();
    }

    public function round()
    {
        return $this->matchable;
    }

    public function nextMatch()
    {
    	return $this->belongsTo(Match::class, 'next_match_id', 'id');
    }

    public function previousMatches()
    {
        return Match::where('next_losers_match_id', $this->id)->orWhere('next_match_id', $this->id);

    	// return $this->hasMany(Match::class, 'next_match_id');
    }

    public function getPreviousMatchesAttribute()
    {
        return $this->previousMatches()->get();
    }

    public function nextLosersMatch()
    {
        return $this->belongsTo(Match::class, 'next_losers_match_id', 'id');
    }

    public function p1()
    {
        return $this->morphTo()->withoutGlobalScope('regular');
    }

    public function p2()
    {
        return $this->morphTo()->withoutGlobalScope('regular');
    }

    public function registrations()
    {
        return collect([$this->p1->registrations(), $this->p2->registrations()]);
    }

    public function p1label()
    {
        if (!is_null($this->p1)) return '';
        if ($this->previousMatches->count() == 0) return '';

        $match = $this->previousMatches->get(0)->id;
        $str = 'Sieger des Spiels';

        if ($this->round()->name == 'DoKo Runde 1') $str = 'Verlierer des Spiels';

        return "{$str} {$match}";
    }

    public function p2label()
    {
        if (!is_null($this->p2)) return '';
        if ($this->previousMatches->count() == 0) return '';

        $str = 'Sieger des Spiels';

        $match = $this->previousMatches->count() == 2 ? $this->previousMatches->get(1)->id : $this->previousMatches->get(0)->id;

        if (!$this->round()->isWinner()) $str = 'Verlierer des Spiels';

        return "{$str} {$match}";
    }

    public function stop($request)
    {
        $this->update([
            'result_id' => $request->result_id,
            'status' => self::$FINISHED,
        ]);

        if ($request->sets)
        {
            $this->sets()->delete();

            foreach ($request->sets as $set)
            {
                $this->sets()->create(['points' => $set['points']]);
            }
        }

        if ($this->table_id)
        {
            Table::whereId($this->table_id)->update(['busy' => false]);
        }
    }

    public function determineWinner($resultId)
    {
        if ($this->hasByes())
        {
            return $this->determineWinnerOfByeMatch();
        }

        if (is_null($resultId) || $resultId == 0)
        {
            return 0;
        }

        $result = Result::find($resultId);

        return $result->left > $result->right ? 1 : 2;
    }

    public function determineWinnerOfByeMatch()
    {
        return $this->isBye($this->p2) ? 1 : 2;
    }

    public function getWinner()
    {
        return $this->winner == 1 ? $this->p1 : $this->p2;
    }

    public function getLoser()
    {
        return $this->winner == 1 ? $this->p2 : $this->p1;
    }

    public function displaySets()
    {
        if (is_null($this->result_id))
        {
            if ($this->winner == 1)
            {
                return '(0 0 0)';
            }

            if ($this->winner == 2)
            {
                return '(-0 -0 -0)';
            }

            return null;
        }

        return '(' . implode(' ', $this->sets->pluck('points')->all()) . ')';
    }

    public function leftResult()
    {
        if ($this->result)
        {
            return $this->result->left;
        }

        return $this->winner == 1 ? 'check' : '&nbsp;';
    }

    public function rightResult()
    {
        if ($this->result)
        {
            return $this->result->right;
        }

        return $this->winner == 2 ? 'check' : '&nbsp;';
    }

    public function isBye($participant)
    {
        return is_null($participant) ? false : $participant->isBye();
    }

    public function isStarted()
    {
        return $this->status == self::$STARTED;
    }

    public function isFinished()
    {
        return $this->status == self::$FINISHED;
    }

    public function hasByes()
    {
        return $this->isBye($this->p1) || $this->isBye($this->p2);
    }

    public function isAvailable($participant)
    {
        return is_null($participant) ? false : $participant->isAvailable();
    }

    public function hasAvailables()
    {
        return $this->isAvailable($this->p1) || $this->isAvailable($this->p2);
    }

    public function isEmpty()
    {
        return is_null($this->p1_id) || is_null($this->p2_id);
    }

    public function isRegular()
    {
        return !($this->hasByes() || $this->hasAvailables() || $this->isEmpty());
    }

    public function scopeStarted($query)
    {
        return $query->whereStatus(self::$STARTED);
    }

    public function scopeFinished($query)
    {
        return $query->whereStatus(self::$FINISHED);
    }

    public function scopeNotFinished($query)
    {
        return $query->where('status', '<>', self::$FINISHED);
    }

    public function scopeFilled($query)
    {
        return $query->whereNotNull('p1_id')->whereNotNull('p2_id');
    }

    public function isFilled()
    {
        return ! $this->isEmpty();
    }

}
