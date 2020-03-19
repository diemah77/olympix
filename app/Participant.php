<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Participant extends Model
{
	protected $casts = [
        'bye' => 'boolean',
        'available' => 'boolean',
        'seed' => 'integer',
        'defeats' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($participant)
        {
            $participant->participantable->delete();
        });

        static::addGlobalScope('regular', function (Builder $builder) {
            $builder->whereAvailable(false);
        });
    }

    public function scopeAvailable($query)
    {
        return $query->withoutGlobalScope('regular')->whereAvailable(true);
    }

    public function scopeBye($query)
    {
        return $query->whereBye(true);
    }

    public function participantable()
    {
        return $this->morphTo();
    }

    public function championship()
    {
    	return $this->belongsTo(Championship::class);
    }

    public function standing()
    {
        return $this->morphOne(Standing::class, 'participant');
    }

    public function phases()
    {
        return $this->belongsToMany(Phase::class);
    }

    public function registrations()
    {
        return $this->participantable->registrations();
    }

    public function isBye()
    {
    	return $this->bye;
    }

    public function isAvailable()
    {
        return $this->available;
    }

    public function isSuspended()
    {
        return $this->defeats > 1;
    }

    public function isSeeded()
    {
        return $this->seed > 0;
    }

    public function ttr()
    {
        if ($this->bye) return 0;

        return $this->participantable->ttr();
    }

    public function fullname()
    {
        if ($this->bye) return '- Freilos -';

        return $this->participantable->fullname();
    }

    public function lastname()
    {
        if ($this->bye) return '- Freilos -';

        return $this->participantable->lastname();
    }
}
