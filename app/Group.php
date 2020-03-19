<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Group extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::created(function ($group)
        {
            $group->phase->increment('groups_count');
        });

        static::deleting(function ($group)
        {
            $group->matches->each->delete();
            $group->standings()->delete();
        });

        static::deleted(function ($group)
        {
            $group->phase->decrement('groups_count');
        });
    }

    public function getName()
    {
        return $this->name;
    }

    public function matches()
    {
        return $this->morphMany(Match::class, 'matchable');
    }

    public function phase()
    {
        return $this->belongsTo(GroupPhase::class, 'group_phase_id');
    }

    public function standings()
    {
        return $this->hasMany(Standing::class);
    }

    public function roundMatchCount()
    {
        $count = $this->standings->count();

        if ($count & 1)
        {
            // ungerade
            return ($count - 1) / 2;
        }

        return $count / 2;
    }

    public function matchesGrid()
	{
        $standings = $this->standings()->withoutGlobalScopes()->with([
            'participant.participantable' => function (MorphTo $morphTo) {
                $morphTo->morphWith([
                    Single::class => ['registration.player'],
                    Double::class => ['r1.player', 'r2.player']
                ]);
            }
        ])
        ->orderBy('participant_id')->get();

        $rivals = $standings;

        $data = collect();
        $top = (object)[];
        $top->col_0 = (object)[
            'result_cell' => false,
            'label' => ''
        ];

        // Iteriere über jeden Teilnehmer
        foreach ($standings as $i => $standing)
        {
            $top->{'col_' . ($i + 1)} = (object)[
                'result_cell' => false,
                'label' => $standing->participant->lastname()
            ];

            $item = (object)[];
            $item->col_0 = (object)[
                'result_cell' => false,
                'label' => $standing->participant->lastname()
            ];

            foreach ($rivals as $j => $rival)
            {
                if ($i != $j)
                {
                    $match = $this->matches->first(function ($match) use ($standing, $rival)
                    {
                        return in_array($match->p1_id, [$standing->participant->id, $rival->participant->id]) &&
                        in_array($match->p2_id, [$standing->participant->id, $rival->participant->id]);
                    });

                    $label = '';
                    if ($match->result)
                    {
                        $label = $j < $i ? $match->result->reversedLabel() : $match->result->label();
                    }

                    $item->{'col_' . ($j + 1)} = (object) [
                        'result_cell' => true,
                        'label' => $label
                    ];
                }
                else
                {
                    $item->{'col_' . ($j + 1)} = (object) [
                        'result_cell' => false,
                        'label' => ''
                    ];
                }
            }

            $data->push($item);
        }

        return $data->prepend($top);
    }
}
