<?php

namespace App\Http\Controllers;

use App\Phase;
use App\Tournament;
use Inertia\Inertia;
use App\Championship;
use App\Http\Controllers\Controller;

class PhaseController extends Controller
{
    function edit(Tournament $tournament, Championship $championship, Phase $phase)
    {
        if ($phase->isGroup())
        {
            $view = 'championships/groupPhase';

            $phaseable = [
                'group_size' => $phase->phaseable->group_size,
                'group_count' => $phase->phaseable->group_count,
                'qualifieds' => $phase->phaseable->qualifieds(),
                'qualified_id' => $phase->phaseable->qualified_id,
                'extra_qualified_id' => $phase->phaseable->extra_qualified_id,
                'groups' => $phase->phaseable->groups->transform(function($g)
                {
                    return [
                        'id' => $g->id,
                        'name' => $g->name,
                        'standings' => $g->standings->transform(function($s)
                        {
                            return [
                                'id'=> $s->id,
                                'name' => $s->isSeeded() ? "{$s->participant->lastname() ($s->participant->seed)}" : $s->participant->lastname(),
                                'seed' => $s->participant->seed
                            ];
                        })
                        ->sortByDesc('seed')
                        ->values()
                    ];
                }),
            ];
        }
        else
        {
            $view = 'championships/koPhase';

            $phaseable = [
                'rounds_count' => $phase->phaseable->rounds_count,
                'rounds' => $phase->phaseable->rounds->transform(function($r)
                {
                    return [
                        'id' => $r->id,
                        'name' => $r->getName(),
                        'isWinner' => $r->isWinner(),
                        'matches' => $r->matches()->orderBy('order')->get()->transform(function($m)
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
                                    'seed' => $m->p1 && $m->p1->seed > 0 ? $m->p1->seed : '&nbsp;'
                                ],
                                'p2' => [
                                    'id' => $m->p2 ? $m->p2->id : '',
                                    'isBye' => $m->p2 ? $m->p2->isBye() : true,
                                    'fullname' => $m->p2 ? $m->p2->fullname() : '&nbsp;',
                                    'seed' => $m->p2 && $m->p2->seed > 0 ? $m->p2->seed : '&nbsp;'
                                ]
                            ];
                        })
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
            'phase' => [
                'id' => $phase->id,
                'name' => $phase->name(),
                'type' => $phase->type(),
                'draw_type' => $phase->draw_type,
                'drawTypeList' => $phase->drawTypeList(),
                'canBeDrawn' => $phase->canBeDrawn(),
                'isDrawn' => $phase->isDrawn(),
                'isStarted' => $phase->isStarted(),
                'canBeStarted' => $phase->canBeStarted(),
            ]  + $phaseable,
            'participants_count' => $championship->participants->count(),
        ]);
    }

    public function start(Tournament $tournament, Championship $championship, Phase $phase)
    {
        $phase->update(['status' => Phase::STATUS_STARTED]);

        return response('OK', 201);
    }
}
