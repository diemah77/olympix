<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Handicap extends Model
{
    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }
}
