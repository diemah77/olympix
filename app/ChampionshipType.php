<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ChampionshipType extends Model
{
    public function isSingles()
    {
        return $this->name == 'Einzel';
    }

    public function isDoubles()
    {
        return $this->name == 'Doppel';
    }
}
