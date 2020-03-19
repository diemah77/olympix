<?php

namespace App;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Tournament extends Model
{
    use HasStatus;

    protected $casts = [
        'table_count' => 'integer',
        'status' => 'integer'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($tournament)
        {
        	for ($i = 1; $i <= $tournament->tables_count; $i++)
	        {
            	$tournament->tables()->create([
                    'name' => 'Tisch ' . $i
                ]);
	        }

            // factory(Viewer::class)->create(['tournament_id' => $tournament->id]);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function viewer()
    {
        return $this->hasOne(Viewer::class);
    }

    public function championships()
    {
    	return $this->hasMany(Championship::class);
    }

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function tables()
    {
    	return $this->hasMany(Table::class);
    }

    public function matches()
    {
        return $this->hasMany(Match::class);
    }

    public function allChampionshipsFinished()
    {
        return $this->championships->filter(function($championship)
        {
            return !$championship->isFinished();
        })
        ->count() == 0;
    }

    public function hasStartedChampionship()
    {
        return $this->championships->filter(function($championship)
        {
            return $championship->isStarted();
        })
        ->count() > 0;
    }

    public function updateStatus()
    {
        if ($this->allChampionshipsFinished())
        {
            return $this->update(['status' => self::$FINISHED]);
        }

        if ($this->hasStartedChampionship())
        {
            return $this->update(['status' => self::$STARTED]);
        }
    }

    public function cacheKey()
    {
        return 'tournament.' . $this->id . '.matches';
    }

    public function cacheMatches()
    {
        $matches = $this->matches()
            ->with([
                'matchable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Round::class => ['phase'],
                        Group::class => ['phase']
                    ]);
                },
                'p1' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Participant::class => ['participantable' => function (MorphTo $morphTo) {
                            $morphTo->morphWith([
                                Single::class => ['registration.player'],
                                Double::class => ['r1.player', 'r2.player']
                            ]);
                        }],
                    ]);
                },
                'p2' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Participant::class => ['participantable' => function (MorphTo $morphTo) {
                            $morphTo->morphWith([
                                Single::class => ['registration.player'],
                                Double::class => ['r1.player', 'r2.player']
                            ]);
                        }],
                    ]);
                },
                'championship',
                'result',
                'sets',
                'table'
            ])
            ->get();

        Cache::put($this->cacheKey(), $matches, now()->addHour());
    }

    public function cachedMatches()
    {
        if (Cache::has($this->cacheKey()))
        {
            return Cache::get($this->cacheKey());
        }

        $this->cacheMatches();

        return Cache::get($this->cacheKey());
    }
}
