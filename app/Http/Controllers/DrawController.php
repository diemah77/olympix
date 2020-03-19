<?php

namespace App\Http\Controllers;

use App\Phase;
use App\Tournament;
use App\Championship;
use App\Events\DrawCreated;
use App\Events\DrawDestroyed;
use App\Http\Controllers\Controller;

class DrawController extends Controller
{
    public function store(Tournament $tournament, Championship $championship, Phase $phase)
    {
        $rules = [
            'draw_type' => 'required|integer',
        ];

        if ($phase->isGroup())
        {
            $rules = [
                'group_size' => 'required|integer|min:1',
                'qualified_id' => 'required|integer',
                'draw_type' => 'required|integer',
            ];
        }

       $this->validate(request(), $rules);

        $phase->phaseable->generateDraw(request());
        $phase->update(['draw_type' => request()->draw_type, 'status' => Phase::STATUS_PREPARED]);

        event(new DrawCreated($tournament));

        return response('OK', 201);
    }

    public function destroy(Tournament $tournament, Championship $championship, Phase $phase)
    {
        $phase->phaseable->reset();
        $phase->update(['status' => null]);

        event(new DrawDestroyed($tournament));

        return response('OK', 200);
    }
}
