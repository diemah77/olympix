<?php

namespace App\Http\Controllers;

use App\Double;
use App\Competitor;
use App\Tournament;
use App\DoublesDraw;
use App\Championship;
use App\Http\Controllers\Controller;

class DoubleController extends Controller
{
    public function store(Tournament $tournament, Championship $championship)
    {
        $championship->update(['doubles_draw_type' => request()->mode]);
        $doubles = DoublesDraw::generate($championship, request()->mode);
    	$championship->participants()->where('participantable_type', 'App\Double')->get()->each->delete();

        foreach ($doubles as $double)
        {
            $championship->addDouble($double->first(), $double->last());
        }

    	return response(['msg' => 'Doppel ausgelost', 200]);
    }

    public function destroy(Tournament $tournament, Championship $championship)
    {
        $championship->participants()->where('participantable_type', 'App\Double')->get()->each->delete();

        foreach ($championship->phases as $phase)
        {
            $phase->phaseable->reset();
        }

    	return response(['msg' => 'Alle Doppel gelöscht', 200]);
    }
}
