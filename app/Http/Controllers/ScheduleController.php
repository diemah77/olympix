<?php

namespace App\Http\Controllers;

use App\Phase;
use App\Single;
use App\Double;
use App\Result;
use App\Tournament;
use Inertia\Inertia;
use App\Championship;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ScheduleController extends Controller
{
    public function index(Tournament $tournament)
    {
        $hasFreeTables = $tournament->tables()->whereBusy(false)->count() > 0;

        $matches = $tournament->cachedMatches()->filter(function ($m) use ($hasFreeTables)
        {
            return $m->isStarted() || ($hasFreeTables && $m->isRegular() && !$m->isFinished() && !$m->hasInvolvedPlayers());
        })
        ->transform(function ($m)
        {
            return [
                'id' => $m->id,
                'championship' => $m->championship->name,
                'championship_id' => $m->championship->id,
                'winningSets' => $m->championship->winningSets(),
                'handicap' => $m->handicap(),
                'setsCount' => $m->championship->sets,
                'phase' => $m->matchable->phase->phase->name(),
                'matchable' => $m->matchable->getName(),
                'p1' => $m->p1 ? $m->p1->fullname() : '',
                'p2' => $m->p2 ? $m->p2->fullname() : '',
                'statusName' => $m->statusName(),
                'result_id' => $m->result_id,
                'sets' => $m->sets,
                'table_id' => $m->table_id,
                'table' => optional($m->table)->name,
                'isStarted' => $m->isStarted(),
                'isRegular' => $m->isRegular()
            ];
        })
        ->values();

        return Inertia::render('schedule/index', [
            'matches' => $matches,
            'tables' => $tournament->tables->transform(function ($t)
            {
                return [
                    'id' => $t->id,
                    'name' => $t->displayName(),
                    'busy' => $t->busy
                ];
            }),
            'results' => Result::all()->transform(function($r)
            {
                return [
                    'id' => $r->id,
                    'label' => $r->label(),
                    'setCount' => $r->setCount(),
                    'size' => $r->size
                ];
            }),
        ]);
    }

    public function tables(Tournament $tournament)
    {
        $runningMatches = $tournament->cachedMatches()->filter(function ($m)
        {
            return $m->isStarted();
        })
        ->values();

        return Inertia::render('schedule/tables', [
            'tournament' => $tournament,
            'tables' => $tournament->tables->transform(function ($t) use ($runningMatches)
            {
                $match = $runningMatches->first(function ($m) use ($t)
                {
                    return $m->table_id == $t->id;
                });

                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'busy' => $t->busy,
                    'horizontal' => $t->horizontal,
                    'match' => $match ? [
                        'p1' => $match->p1->fullname(),
                        'p2' => $match->p2->fullname()
                    ] : null
                ];
            })
        ]);
    }

    public function show(Tournament $tournament, Championship $championship)
    {
        $hasFreeTables = $tournament->tables()->whereBusy(false)->count() > 0;

        $matches = $tournament->cachedMatches()->filter(function ($m) use ($championship)
        {
            return $m->championship_id == $championship->id;
        })
        ->transform(function ($m) use ($hasFreeTables)
        {
            return [
                'id' => $m->id,
                'championship' => $m->championship->name,
                'championship_id' => $m->championship_id,
                'table' => optional($m->table)->name,
                'statusName' => $m->statusName(),
                'isStarted' => $m->isStarted(),
                'isFinished' => $m->isFinished(),
                'isRegular' => $m->isRegular(),
                'handicap' => $m->handicap(),
                'relevant' => $m->isStarted() || ($hasFreeTables && $m->isRegular() && !$m->isFinished() && !$m->hasInvolvedPlayers()),
                'p1' => [
                    'fullname' => $m->p1 ? $m->p1->fullname() : '',
                ],
                'p2' => [
                    'fullname' => $m->p2 ? $m->p2->fullname() : '',
                ],
                'phase' => $m->matchable->phase->phase->name(),
                'matchable' => $m->matchable->getName(),
                'result' => $m->result ? $m->result->label() : '',
                'result_id' => $m->result_id,
                'sets' => $m->sets
            ];
        })
        ->values();

        return Inertia::render('schedule/show', [
            'tables' => $tournament->tables->transform(function($t)
            {
                return [
                    'id' => $t->id,
                    'name' => $t->displayName(),
                    'busy' => $t->busy,
                ];
            }),
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'winningSets' => $championship->winningSets(),
                'phases' => $championship->phases()
                    ->orderBy('order')
                    ->get()
                    ->transform(function($p)
                    {
                        return [
                            'id' => $p->id,
                            'name' => $p->name(),
                        ];
                    }),
            ],
            'results' => $championship->results()->transform(function($r)
            {
                return [
                    'id' => $r->id,
                    'label' => $r->label(),
                    'setCount' => $r->setCount()
                ];
            }),
            'matches' => $matches
        ]);
    }

    public function phase(Tournament $tournament, Championship $championship, Phase $phase)
    {
        if ($phase->isGroup())
        {
            $view = 'schedule/groupPhase';
            $groups = $phase->phaseable->groups()->with([
                'standings.participant.participantable' => function (MorphTo $morphTo) {
                    $morphTo->morphWith([
                        Single::class => ['registration.player'],
                        Double::class => ['r1.player', 'r2.player']
                    ]);
                }
            ])
            ->get();

            $phaseable = [
                'group_size' => $phase->phaseable->group_size,
                'group_count' => $phase->phaseable->group_count,
                'qualifieds' => $phase->phaseable->qualifieds(),
                'qualified_id' => $phase->phaseable->qualified_id,
                'extra_qualified_id' => $phase->phaseable->extra_qualified_id,
                'groups' => $groups->transform(function ($g)
                {
                    return [
                        'id' => $g->id,
                        'name' => $g->name,
                        'standings' => $g->standings->transform(function ($s)
                        {
                            return [
                                'id'=> $s->id,
                                'wins' => $s->wins,
                                'defeats' => $s->defeats,
                                'setsWon' => $s->setsWon,
                                'setsLost' => $s->setsLost,
                                'pointsWon' => $s->pointsWon,
                                'pointsLost' => $s->pointsLost,
                                'matches' => $s->matches(),
                                'sets' => $s->sets(),
                                'points' => $s->points(),
                                'name' => $s->participant->lastname(),
                            ];
                        }),
                        'matchGrid' => $g->matchesGrid()
                    ];
                }),
            ];
        }
        else
        {
            $view = 'schedule/koPhase';
            $matches = $tournament->cachedMatches();

            $phaseable = [
                'rounds' => $phase->phaseable->rounds->transform(function($r) use ($matches)
                {
                    return [
                        'id' => $r->id,
                        'name' => $r->getName(),
                        'isWinner' => $r->isWinner(),
                        'matches' => $matches->filter(function ($m) use ($r)
                        {
                            return $m->matchable->id == $r->id && $m->matchable_type == 'App\Round';
                        })
                        ->sortBy('id')
                        ->transform(function ($m)
                        {
                            return [
                                'id' => $m->id,
                                'championship' => $m->championship->name,
                                'championship_id' => $m->championship_id,
                                'matchable' => $m->matchable->getName(),
                                'winner' => $m->winner,
                                'isStarted' => $m->isStarted(),
                                'isRegular' => $m->isRegular(),
                                'statusName' => $m->statusName(),
                                'handicap' => $m->handicap(),
                                'result_id' => $m->result_id,
                                'table_id' => $m->table_id,
                                'result' => [
                                    'left' => $m->leftResult(),
                                    'right' => $m->rightResult(),
                                    'sets' => $m->sets->map(function ($set)
                                    {
                                        return [
                                            'points' => $set->displayPoints()
                                        ];
                                    })
                                ],
                                'p1' => [
                                    'id' => $m->p1 ? $m->p1->id : '',
                                    'isBye' => $m->p1 ? $m->p1->isBye() : true,
                                    'fullname' => $m->p1 ? $m->p1->fullname() : '&nbsp;',
                                ],
                                'p2' => [
                                    'id' => $m->p2 ? $m->p2->id : '',
                                    'isBye' => $m->p2 ? $m->p2->isBye() : true,
                                    'fullname' => $m->p2 ? $m->p2->fullname() : '&nbsp;',
                                ],
                                'sets' => $m->sets
                            ];
                        })->values()
                    ];
                })
            ];
        }

        return Inertia::render($view, [
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'phases' => $championship->phases()->orderBy('order')->get()->transform(function($p) use ($phase)
                {
                    return [
                        'id' => $p->id,
                        'name' => $p->name(),
                        'isCurrent' => $p->id == $phase->id
                    ];
                })
            ],
            'tables' => $tournament->tables()->orderBy('id')->get()->transform(function ($t)
            {
                return [
                    'id' => $t->id,
                    'name' => $t->displayName(),
                    'busy' => $t->busy
                ];
            }),
            'results' => $championship->results()->transform(function($r)
            {
                return [
                    'id' => $r->id,
                    'label' => $r->label(),
                    'setCount' => $r->setCount()
                ];
            }),
            'phase' => [
                'id' => $phase->id,
                'name' => $phase->name(),
            ]  + $phaseable
        ]);
    }
}
