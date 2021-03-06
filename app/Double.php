<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Double extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::deleted(function ($double)
        {
            $double->participant()->delete();
        });
    }

    public function participant()
    {
        return $this->morphOne(Participant::class, 'participantable');
    }

    public function r1()
    {
        return $this->belongsTo(Registration::class, 'registration1_id');
    }

    public function r2()
    {
        return $this->belongsTo(Registration::class, 'registration2_id');
    }

    public function registrations()
    {
        return collect()->push($this->r1)->push($this->r2);
    }

    public function ttr()
    {
        return intval(($this->r1->player->ttr + $this->r2->player->ttr) / 2);
    }

    public function fullname()
    {
        return $this->lastname();
    }

    public function lastname()
    {
        return "{$this->r1->player->lastname} / {$this->r2->player->lastname}";
    }
}
