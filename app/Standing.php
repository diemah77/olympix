<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Standing extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('sorted', function ($builder)
        {
            $builder->sorted();
        });
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('wins', 'desc')
            ->orderByRaw('(sets_won - sets_lost) DESC')
            ->orderByRaw('(points_won - points_lost) DESC');
    }

    public function participant()
    {
    	return $this->morphTo();
    }

    public function group()
    {
        return $this->belongsTo(Group::class);
    }

    public function matchCount()
    {
    	return $this->wins + $this->defeats;
    }

    public function matches()
    {
    	return $this->matchCount() > 0 ? "{$this->wins}:{$this->defeats}" : '0:0';
    }

    public function sets()
    {
    	return $this->matchCount() > 0 ? "{$this->sets_won}:{$this->sets_lost}" : '0:0';
    }

    public function diffSets()
    {
    	return $this->sets_won - $this->sets_lost;
    }

    public function points()
    {
        // return $this->matchCount() > 0 ? "{$this->points_won} : {$this->points_lost}" : '0 : 0';
        if ($this->matchCount() > 0)
        {
            $balance = $this->points_won - $this->points_lost;

            if ($balance > 0)
            {
                return '+' . $balance;
            }

            return $balance;
        }

    	return 0;
    }

    public function diffPoints()
    {
    	return $this->points_won - $this->points_lost;
    }

}
