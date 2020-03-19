<?php

namespace App\Http\Controllers;

use App\Tournament;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class TournamentController extends Controller
{
    public function index()
    {
        return Inertia::render('tournaments/index', [
            'tournaments' => auth()->user()->tournaments->transform(function($t)
            {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'tables_count' => $t->tables_count,
                    'championships_count' => $t->championships_count,
                    'statusName' => $t->statusName(),
                ];
            })
        ]);
    }

    public function create()
    {
        return Inertia::render('tournaments/edit', [
            'tournament' => [
                'name' => '',
                'tables_count' => '',
            ],
            'mode' => 'create'
        ]);
    }

    public function store()
    {
        $data = $this->validate(request(), [
            'name' => 'required',
            'tables_count' => 'required|numeric|min:1',
        ]);

        auth()->user()->tournaments()->create($data);

        return response('OK', 201);
    }

    public function edit(Tournament $tournament)
    {
        return Inertia::render('tournaments/edit', [
            'tournament' => [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tables_count' => $tournament->tables_count
            ],
            'mode' => 'edit'
        ]);
    }

    public function update(Tournament $tournament)
    {
        $data = $this->validate(request(), [
            'name' => 'required'
        ]);

        $tournament->update($data);

        return response('OK', 200);
    }
}
