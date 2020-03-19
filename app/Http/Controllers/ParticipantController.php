<?php

namespace App\Http\Controllers;

use App\Tournament;
use App\Participant;
use App\Championship;
use App\Http\Controllers\Controller;

class ParticipantController extends Controller
{
    public function update(Tournament $tournament, Championship $championship)
    {
        if (request()->has('participants'))
        {
            foreach (request()->participants as $participant)
            {
                Participant::whereId($participant['id'])->update(['seed' => $participant['seed']]);
            }

            return response('OK', 200);
        }
    }
}
