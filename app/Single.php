<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Single extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($single)
        {
            $single->participant()->delete();
        });
    }

    public function participant()
    {
        return $this->morphOne(Participant::class, 'participantable');
    }

	public function registration()
	{
		return $this->belongsTo(Registration::class);
	}

    public function registrations()
    {
        return collect()->push($this->registration);
    }

	public function ttr()
    {
        return $this->registration->player->ttr();
    }

    public function fullname()
    {
        return $this->registration->player->fullname();
    }

    public function lastname()
    {
        return $this->registration->player->lastname();
    }
}
