<?php

namespace App\Http\Controllers;

use App\Tournament;
use Inertia\Inertia;
use App\Championship;
use App\ChampionshipType;
use App\ChampionshipSystem;
use App\Http\Controllers\Controller;

class ChampionshipController extends Controller
{
    public function index(Tournament $tournament)
    {
        $tournament->load('championships.system', 'championships.type', 'championships.registrations');

        return Inertia::render('championships/index', [
            'championships' => $tournament->championships->transform(function($c)
            {
                return [
                    'id' => $c->id,
                    'name' => $c->name,
                    'registrations_count' => $c->registrations->count(),
                    'systemName' => $c->system->name,
                    'typeName' => $c->type->name,
                    'active' => $c->active,
                    'statusName' => $c->statusName()
                ];
            })
        ]);
    }

    public function create(Tournament $tournament, Championship $championship)
    {
        return Inertia::render('championships/edit', [
            'championship' => [
                'name' => '',
                'system_id' => '',
                'third_place' => false,
                'handicap' => false,
                'reverted_handicap' => false,
                'type_id' => '',
                'sets' => '',
                'setsList' => $championship->setsList(),
                'handicaps' => $championship->getHandicaps(),
                'mode' => 'create',
            ],
            'systems' => ChampionshipSystem::all(),
            'types' => ChampionshipType::all(),
            'mode' => 'create'
        ]);
    }

    public function store(Tournament $tournament)
    {
        $data = $this->validate(request(), [
            'name' => 'required',
            'system_id' => 'required',
            'third_place' => '',
            'type_id' => 'required',
            'sets' => 'required',
            'handicap' => '',
            'reverted_handicap' => '',
        ]);

        $championship = $tournament->championships()->create($data);

        if ($data['handicap'])
        {
            $championship->saveHandicaps(request()->handicaps);
        }

        return response(['id' => $championship->id], 200);
    }

    public function edit(Tournament $tournament, Championship $championship)
    {
        return Inertia::render('championships/edit', [
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'statusName' => $championship->statusName(),
                'system_id' => $championship->system_id,
                'third_place' => $championship->third_place,
                'handicap' => $championship->handicap,
                'reverted_handicap' => $championship->reverted_handicap,
                'type_id' => $championship->type_id,
                'sets' => $championship->sets,
                'setsList' => $championship->setsList(),
                'handicaps' => $championship->getHandicaps(),
                'isSingles' => $championship->isSingles(),
                'isDoubles' => $championship->isDoubles(),
                'mode' => 'edit',
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
            'systems' => ChampionshipSystem::all(),
            'types' => ChampionshipType::all(),
            'mode' => 'edit'
        ]);
    }

    public function players(Tournament $tournament, Championship $championship)
    {
        $championship->load('registrations');

        $players = $tournament->players()
            ->orderByRaw('LENGTH(lastname) asc')
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get()
            ->transform(function($p) use ($championship)
            {
                return [
                    'id' => $p->id,
                    'fullname' => $p->fullname(),
                    'ttr' => $p->ttr,
                    'isRegistered' => $championship->isPlayerRegistered($p)
                ];
            });

        return Inertia::render('championships/players', [
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'statusName' => $championship->statusName(),
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
            'initialRegistrations' => $championship->registrations,// TODO: transform
            'initialPlayers' => $players,
        ]);
    }

    public function doubles(Tournament $tournament, Championship $championship)
    {
        return Inertia::render('championships/doubles', [
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'statusName' => $championship->statusName(),
                'doubles_draw_type' => $championship->doubles_draw_type,
                'doublesDrawList' => $championship->doublesDraw(),
                'participants' => $championship->participants->transform(function ($p)
                {
                    return [
                        'id' => $p->id,
                        'fullname' => $p->fullname(),
                        'ttr' => $p->ttr()
                    ];
                })->sortByDesc('ttr')->values(),
                'registrations' => $championship->registrations->load('player')->transform(function ($r)
                {
                    return [
                        'id' => $r->id,
                        'fullname' => $r->player->fullname(),
                        'ttr' => $r->player->ttr,
                    ];
                })->sortByDesc('ttr')->values(),
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
            ]
        ]);
    }

    public function seeding(Tournament $tournament, Championship $championship)
    {
        $championship->load('participants');

        $participants = $championship->participants->filter(function ($p)
        {
            return $p->bye == 0;
        })
        ->transform(function($p)
        {
            return [
                'id' => $p->id,
                'fullname' => $p->fullname() . ' (' . $p->ttr() . ')',
                'seed' => $p->seed,
                'isSeeded' => $p->isSeeded(),
                'ttr' => $p->ttr()
            ];
        })
        ->sortByDesc('ttr')
        ->values();

        return Inertia::render('championships/seeding', [
            'championship' => [
                'id' => $championship->id,
                'name' => $championship->name,
                'statusName' => $championship->statusName(),
                'isSingles' => $championship->isSingles(),
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
            'initialParticipants' => $participants,
        ]);
    }

    public function update(Tournament $tournament, Championship $championship)
    {
        $data = $this->validate(request(), [
            'name' => 'required',
            'system_id' => 'required',
            'third_place' => '',
            'type_id' => 'required',
            'sets' => 'required',
            'handicap' => '',
            'reverted_handicap' => '',
        ]);

        // Wenn wichtige Felder geändert worden sind, dann Phasen zurücksetzen
        if (
            $data['system_id'] != $championship->system_id ||
            $data['third_place'] != $championship->third_place ||
            $data['type_id'] != $championship->type_id ||
            $data['sets'] != $championship->sets
        )
        {
            $championship->update($data);

            $championship->reset();
            $championship->createPhases();
        }

        $championship->update($data);

        return response('OK', 200);
    }

    public function destroy(Tournament $tournament, Championship $championship)
    {
        $championship->delete();

        return response('OK', 200);
    }
}
