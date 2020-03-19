<?php

namespace App\Http\Controllers;

use App\Player;
use App\Tournament;
use App\Championship;
use App\Http\Controllers\Controller;

class RegistrationController extends Controller
{
    public function store(Tournament $tournament, Championship $championship, Player $player)
    {
        return response(['registration' => $championship->register($player)], 201);
    }

    public function storeAll(Tournament $tournament, Championship $championship)
    {
        $registrations = collect();

        if (request()->has('players'))
        {
            $championship->registrations->each->delete();

            foreach(request()->players as $player)
            {
                $registrations = $registrations->push($championship->register(Player::find($player['id'])));
            }
        }

        return response(['registrations' => $registrations], 200);
    }

    public function destroy(Tournament $tournament, Championship $championship, Player $player)
    {
        $championship->registrations()->where('player_id', $player->id)->get()->each->delete();

        return response('OK', 200);
    }
}
