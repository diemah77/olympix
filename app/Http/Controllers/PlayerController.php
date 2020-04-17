<?php

namespace App\Http\Controllers;

use App\Player;
use App\Tournament;
use Inertia\Inertia;
use App\Imports\PlayersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class PlayerController extends Controller
{
    public function index(Tournament $tournament)
    {
        return Inertia::render('players/index', [
            'initialPlayers' => $tournament->players->transform(function($p)
            {
                return [
                    'id' => $p->id,
                    'fullname' => $p->fullname(),
                    'ttr' => $p->ttr,
                    'club' => $p->club
                ];
            })
        ]);
    }

    public function create()
    {
        return Inertia::render('players/edit', [
            'player' => [
                'firstname' => '',
                'lastname' => '',
                'club' => '',
                'ttr' => '',
            ],
            'mode' => 'create'
        ]);
    }

    public function store(Tournament $tournament)
    {
        $data = $this->validate(request(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'club' => '',
            'ttr' => 'required|numeric|min:800'
        ]);

        $player = $tournament->players()->create($data);

        return response(['id' => $player->id], 201);
    }

    public function edit(Tournament $tournament, Player $player)
    {
        return Inertia::render('players/edit', [
            'player' => [
                'id' => $player->id,
                'firstname' => $player->firstname,
                'lastname' => $player->lastname,
                'fullname' => $player->firstname . ' ' . $player->lastname,
                'club' => $player->club,
                'ttr' => $player->ttr,
                'championships' => $player->championships // TODO: transform
            ],
            'championships' => $tournament->championships,
            'mode' => 'edit'
        ]);
    }

    public function update(Tournament $tournament, Player $player)
    {
        $data = $this->validate(request(), [
            'firstname' => 'required',
            'lastname' => 'required',
            'club' => 'required',
            'ttr' => 'required|numeric|min:800'
        ]);

        $player->update($data);

        return response('OK', 200);
    }

    public function random(Tournament $tournament)
    {
        $players = factory(Player::class, intval(request()->count))->create(['tournament_id' => $tournament->id]);

        return $players->transform(function($p)
        {
            return [
                'id' => $p->id,
                'fullname' => $p->fullname(),
                'ttr' => $p->ttr,
                'club' => $p->club
            ];
        });
    }

    public function import(Tournament $tournament)
    {
        $validator = Validator::make(request()->all(), ['players' => 'file']);

        $validator->after(function ($validator)
        {
            if(! in_array(request()->file('players')->getClientOriginalExtension(), ['csv','xls','xlsx']))
            {
                $validator->errors()->add('players', 'Bitte eines der folgende Formate nutzen: csv, xls, xlsx');
            }
        });

        if ($validator->fails())
        {
            return response($validator->errors(), 422);
        }

        Excel::import(new PlayersImport($tournament), request()->file('players'));

        return response('OK', 200);
    }
}
