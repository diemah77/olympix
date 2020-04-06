<?php

namespace App\Http\Controllers;

use App\Tournament;
use Inertia\Inertia;
use App\Http\Controllers\Controller;

class ResultController extends Controller
{
    public function __invoke($hash)
    {
        $tournament = Tournament::where('route_hash', $hash)->firstOrFail();

        abort_if(!$tournament->published, 404);

        return $tournament->name;
    }
}
