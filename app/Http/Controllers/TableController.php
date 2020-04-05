<?php

namespace App\Http\Controllers;

use App\Tournament;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    public function index(Tournament $tournament)
    {
        return Inertia::render('tournaments/tables', [
            'tournament' => $tournament,
            'initialTables' => $tournament->tables->transform(function($t)
            {
                return [
                    'id' => $t->id,
                    'name' => $t->name,
                    'busy' => $t->busy,
                    'horizontal' => $t->horizontal
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
                'mode' => 'create'
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
                'tables_count' => $tournament->tables_count,
                'mode' => 'edit'
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

    public function rotate(Tournament $tournament)
    {
        foreach ($tournament->tables as $table)
        {
            $table->update(['horizontal' => !$table->horizontal]);
        }

        return response('OK', 200);
    }
}
