<?php

namespace App\Http\Controllers;

use App\Phase;
use App\Tournament;
use Inertia\Inertia;
use App\Championship;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ResultController extends Controller
{
    public function __invoke(Tournament $tournament)
    {
        $championship = $tournament->championships->first();
        $phase = $championship->phases()->orderBy('order')->first();

        return redirect()->route('results.show', [
            'tournament' => $tournament,
            'championship' => $championship,
            'phase' => $phase]);
    }

    public function championship(Tournament $tournament, Championship $championship)
    {
        $phase = $championship->phases()->orderBy('order')->first();

        return redirect()->route('results.show', [
            'tournament' => $tournament,
            'championship' => $championship,
            'phase' => $phase]);
    }

    public function show(Tournament $tournament, Championship $championship, Phase $phase)
    {
        if ($phase->isGroup())
        {
            $view = 'results/groupPhase';
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
            $view = 'results/koPhase';
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
                        ->transform(function ($m)
                        {
                            return [
                                'id' => $m->id,
                                'winner' => $m->winner,
                                'result' => [
                                    'left' => $m->leftResult(),
                                    'right' => $m->rightResult(),
                                    'sets' => $m->displaySets()
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
                                ]
                            ];
                        })->values()
                    ];
                })
            ];
        }

        return Inertia::render($view, [
            'tournament' => [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'hash' => $tournament->hash,
                'published' => $tournament->published,
                'results_route' => $tournament->resultsRoute(),
            ],
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'slug' => $championship->slug,
                'phases' => $championship->phases()
                    ->orderBy('order')
                    ->get()
                    ->transform(function($p)
                    {
                        return [
                            'id' => $p->id,
                            'name' => $p->name(),
                        ];
                    })
            ],
            'phase' => [
                'id' => $phase->id,
                'name' => $phase->name(),
            ]  + $phaseable,
            'championships' => $tournament->championships->transform(function ($c)
            {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'results_route' => $c->resultsRoute()
                ];
            })
        ]);
    }
}
