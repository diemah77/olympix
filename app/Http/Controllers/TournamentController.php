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
                'tables_enforce_assignment' => true,
                'tables_transformations' => [],
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
            'tables_enforce_assignment' => '',
            'tables_transformations' => [],
        ]);

        $tournament = auth()->user()->tournaments()->create($data);

        return response($tournament, 200);
    }

    public function edit(Tournament $tournament)
    {
        return Inertia::render('tournaments/edit', [
            'tournament' => [
                'id' => $tournament->id,
                'name' => $tournament->name,
                'tables_count' => $tournament->tables_count,
                'tables_enforce_assignment' => $tournament->tables_enforce_assignment,
                'qr_code_src' => $tournament->qr_code_src,
                'published' => $tournament->published,
                'results_route' => $tournament->resultsRoute(),
                'mode' => 'edit'
            ],
            'mode' => 'edit'
        ]);
    }

    public function update(Tournament $tournament)
    {
        $data = $this->validate(request(), [
            'name' => 'required',
            'tables_enforce_assignment' => '',
            'tables_transformations' => [],
        ]);

        $tournament->update($data);

        return response($tournament, 200);
    }

    public function tables(Tournament $tournament)
    {
        $tournament->update([
            'tables_rows' => request()->tables_rows,
            'tables_transformations' => request()->tables_transformations
        ]);

        return response('OK', 200);
    }

    public function publish(Tournament $tournament)
    {
        $tournament->publish();

        return response('OK', 200);
    }

    public function unpublish(Tournament $tournament)
    {
        $tournament->unpublish();

        return response('OK', 200);
    }

    public function downloadQRCode(Tournament $tournament)
    {
        return response()->download(
            public_path('/qrcodes/' . $tournament->qr_code_src),
            $tournament->name . ' _Ergebnis_QR-Code.png'
        );
    }
}
