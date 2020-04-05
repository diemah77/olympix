<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
	protected $casts = [
		'busy' => 'boolean'
	];

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public function displayName()
    {
        return 'Tisch ' . $this->name;
    }
}
