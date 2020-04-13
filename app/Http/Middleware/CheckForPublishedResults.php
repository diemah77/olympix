<?php

namespace App\Http\Middleware;

use Closure;
use App\Tournament;
use App\Championship;
use Inertia\Inertia;

class CheckForPublishedResults
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $msg = null;
        $tournament = $request->tournament;

        if (! $tournament->published)
        {
            $msg = 'Die Turnierergebnisse sind noch nicht veröffentlicht.';
        }

        if ($tournament->championships_count == 0)
        {
            $msg = 'Es wurden noch keine Spielklassen ausgelost.';
        }

        if ($msg)
        {
            return Inertia::render('results/empty', [
                'tournament' => [
                    'id' => $tournament->id,
                    'name' => $tournament->name,
                    'hash' => $tournament->hash,
                    'results_route' => $tournament->resultsRoute(),
                ],
                'msg' => $msg
            ]);
        }

        return $next($request);
    }
}
