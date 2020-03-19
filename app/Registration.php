<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($registration)
        {
            $registration->championship->increment('registrations_count');
        });

        static::deleted(function ($registration)
        {
            $registration->championship->decrement('registrations_count');

            if ($registration->single) $registration->single->delete();
            if ($registration->doubles()->exists()) $registration->doubles()->first()->delete();
        });
    }

    public function championship()
    {
        return $this->belongsTo(Championship::class);
    }

	public function player()
    {
    	return $this->belongsTo(Player::class);
    }

    public function single()
    {
    	return $this->hasOne(Single::class);
    }

    public function doubles()
    {
        return Double::where('registration1_id', $this->id)->orWhere('registration1_id', $this->id);
    }
}
