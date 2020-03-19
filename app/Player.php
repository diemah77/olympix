<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $casts = [
        'ttr' => 'integer'
    ];

    public function ttr()
    {
        return $this->ttr;
    }

    public function lastname()
    {
        return $this->lastname;
    }

    public function fullname()
    {
    	return $this->lastname . ', ' . $this->firstname;
    }

    public function registrations()
    {
    	return $this->hasMany(Registration::class);
    }

    public function championships()
    {
        return $this->belongsToMany(Championship::class, 'registrations');
    }
}
