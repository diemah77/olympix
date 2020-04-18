<?php

namespace App;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Set extends Model
{
    public function match()
    {
        return $this->belongsTo(Match::class);
    }

    public function leftPoints()
    {
    	$points = abs(intval($this->points));

    	return $this->isPositive() ? max(11, $points + 2) : $points;
    }

    public function rightPoints()
    {
    	$points = abs(intval($this->points));

    	return $this->isPositive() ? $points : max(11, $points + 2);
    }

    public function isNegative()
    {
    	return Str::startsWith($this->points, '-');
    }

    public function isPositive()
    {
    	return !$this->isNegative();
    }

    public function displayPoints()
    {
        if (intval($this->points) == 0)
        {
            return 0;
        }

        return $this->points > 0 ? '+' . $this->points : $this->points;
    }
}
