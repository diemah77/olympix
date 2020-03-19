<?php

namespace App\Listeners;

class TournamentEventSubscriber
{
    public function updateCache($event)
    {
        logger(get_class($event));
        $event->tournament->cacheMatches();
    }

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\DrawCreated',
            'App\Listeners\TournamentEventSubscriber@updateCache'
        );

        $events->listen(
            'App\Events\DrawDestroyed',
            'App\Listeners\TournamentEventSubscriber@updateCache'
        );

        $events->listen(
            'App\Events\MatchStarted',
            'App\Listeners\TournamentEventSubscriber@updateCache'
        );

        $events->listen(
            'App\Events\MatchStopped',
            'App\Listeners\TournamentEventSubscriber@updateCache'
        );
    }
}
