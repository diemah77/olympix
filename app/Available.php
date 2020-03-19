<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Available extends Model
{
	public $casts = [
		'best' => 'boolean'
	];

    public function registrations()
    {
        return collect();
    }

    public function participant()
    {
        return $this->morphOne(Participant::class, 'participantable')->available();
    }

	public function group()
	{
		return $this->belongsTo(Group::class);
	}

	public function isBye()
    {
    	return false;
    }

    public function isSuspended()
    {
        return false;
    }

    public function ttr()
    {
    	return 0;
    }

    public function fullname()
    {
        return $this->lastname();
    }

    public function lastname()
    {
    	return $this->best ? "Bestplatzierter, Nummer {$this->position}" : "{$this->group->name}, Platz {$this->position}";
    }
}
