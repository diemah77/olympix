<?php

namespace App\Http\Controllers;

use App\Match;
use App\Table;
use App\Tournament;
use App\Championship;
use App\Events\MatchStarted;
use App\Events\MatchStopped;
use App\Rules\ValidMatchSets;
use App\Http\Controllers\Controller;

class MatchController extends Controller
{
    public function start(Tournament $tournament, Championship $championship, Match $match)
    {
        if (request()->filled('table_id'))
        {
        	$table = Table::findOrFail(request()->table_id);
        	$table->update(['busy' => true]);
        }

        $match->start(request());

        event(new MatchStarted($tournament));

    	return response('OK', 200);
    }

    public function stop(Tournament $tournament, Championship $championship, Match $match)
    {
        $rules = [
            'result_id' => 'required|integer',
            'sets' => ['required', new ValidMatchSets(request()->result_id)]
        ];

        $this->validate(request(), $rules);

        $match->stop(request());
        $match->matchable->phase->proceedMatch($match);

        event(new MatchStopped($tournament));

        return response('OK', 200);
    }
}
