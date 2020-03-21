<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Round extends Model
{
	const TYPE_FINAL = 2;
	const TYPE_SEMI = 4;
	const TYPE_QUARTER = 8;
	const TYPE_R16 = 16;
	const TYPE_R32 = 32;
	const TYPE_R64 = 64;
	const TYPE_R128 = 128;

    const SIDE_WINNER = 1;
    const SIDE_LOSER = -1;

    protected static function boot()
    {
        parent::boot();

        static::created(function ($round)
        {
            $round->phase->increment('rounds_count');
        });

        static::deleting(function ($round)
        {
            $round->matches->each->delete();
        });

        static::deleted(function ($round)
        {
            $round->phase->decrement('rounds_count');
        });
    }

    public function matches()
    {
        return $this->morphMany(Match::class, 'matchable');
    }

    public function phase()
    {
        return $this->belongsTo(KoPhase::class, 'ko_phase_id');
    }

    public function typesList()
    {
        return [
            self::TYPE_FINAL => 'Finale',
            self::TYPE_SEMI => 'Halbfinale',
            self::TYPE_QUARTER => 'Viertelfinale',
            self::TYPE_R16 => 'Achtelfinale',
            self::TYPE_R32 => 'Letzte 32',
            self::TYPE_R64 => 'Letzte 64',
            self::TYPE_R128 => 'Letzte 128'
        ];
    }

    public function typeName()
    {
        return isset($this->typesList()[$this->type]) ? $this->typesList()[$this->type] : $this->type;
    }

    public function isSemi()
    {
        return $this->type == self::TYPE_SEMI;
    }

    public function getName()
    {
        if (is_null($this->name)) return $this->typeName();

        return $this->isWinner() ? $this->name : "Verlierer {$this->name}";
    }

    public function isWinner()
    {
        return $this->side == self::SIDE_WINNER;
    }

    public function nextRound()
    {
    	return $this->belongsTo(Round::class, 'next_round_id', 'id');
    }

    public function previousRound()
    {
        if ($this->name == 'Runde 1')
        {
            return $this->belongsTo(Round::class, 'id', 'next_losers_round_id');
        }

        return $this->belongsTo(Round::class, 'id', 'next_round_id');
    }

    public function nextLosersRound()
    {
        return $this->belongsTo(Round::class, 'next_losers_round_id', 'id');
    }

    public function matchCount()
    {
        return $this->matches->count();
    }
}
