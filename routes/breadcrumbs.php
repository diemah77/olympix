<?php

Breadcrumbs::for('tournaments.index', function ($trail) {
    $trail->push('Turniere', route('tournaments.index'));
});

Breadcrumbs::for('tournaments.create', function ($trail) {
    $trail->parent('tournaments.index');
    $trail->push('Turnier erstellen', route('tournaments.create'));
});

Breadcrumbs::for('tournaments.edit', function ($trail, $tournament) {
    $trail->parent('tournaments.index');
    $trail->push('Turnier bearbeiten', route('tournaments.edit',  $tournament));
});

Breadcrumbs::for('tables.index', function ($trail, $tournament) {
    $trail->parent('tournaments.index');
    $trail->push('Tischanordnung', route('tables.index', $tournament));
});

Breadcrumbs::for('championships.index', function ($trail, $tournament) {
    $trail->push($tournament->name, route('championships.index',  $tournament));
});

Breadcrumbs::for('championships.create', function ($trail, $tournament) {
    $trail->parent('championships.index', $tournament);
    $trail->push('Spielklasse erstellen', route('championships.create',  $tournament));
});

Breadcrumbs::for('championships.edit', function ($trail, $tournament, $championship) {
    $trail->parent('championships.index', $tournament);
    $trail->push($championship->name, route('championships.edit', [$tournament, $championship]));
});

Breadcrumbs::for('championship-players', function ($trail, $tournament, $championship) {
    $trail->push($tournament->name, route('championships.index',  $tournament));
    $trail->push($championship->name, route('championships.edit', [$tournament, $championship]));
    $trail->push('Gemeldete Spieler', route('championship-players', [$tournament, $championship]));
});

Breadcrumbs::for('championship-doubles', function ($trail, $tournament, $championship) {
    $trail->push($tournament->name, route('championships.index',  $tournament));
    $trail->push($championship->name, route('championships.edit', [$tournament, $championship]));
    $trail->push('Doppelpaarungen', route('championship-doubles', [$tournament, $championship]));
});

Breadcrumbs::for('championship-seeding', function ($trail, $tournament, $championship) {
    $trail->push($tournament->name, route('championships.index',  $tournament));
    $trail->push($championship->name, route('championships.edit', [$tournament, $championship]));
    $trail->push('Setzliste', route('championship-seeding', [$tournament, $championship]));
});

// Breadcrumbs::for('championship-draw', function ($trail, $tournament, $championship) {
//     $trail->push($tournament->name, route('championships.index',  $tournament));
//     $trail->push($championship->name, route('championships.edit', [$tournament, $championship]));
//     $trail->push('Auslosung', route('championship-draw', [$tournament, $championship]));
// });

Breadcrumbs::for('players.index', function ($trail, $tournament) {
    $trail->push('Spieler', route('players.index', $tournament));
});

Breadcrumbs::for('players.create', function ($trail, $tournament) {
    $trail->parent('players.index', $tournament);
    $trail->push('Spieler erfassen', route('players.create', $tournament));
});

Breadcrumbs::for('players.edit', function ($trail, $tournament, $player) {
    $trail->parent('players.index', $tournament);
    $trail->push('Spieler bearbeiten', route('players.edit', [$tournament, $player]));
});

Breadcrumbs::for('phases.edit', function ($trail, $tournament, $championship, $phase) {
    $trail->push($tournament->name, route('championships.index',  $tournament));
    $trail->push($championship->name, route('championships.edit', [$tournament, $championship]));
    $trail->push('Auslosung', route('phases.edit', [$tournament, $championship, $phase]));
});

Breadcrumbs::for('schedule.index', function ($trail, $tournament) {
    $trail->push('Spielplan', route('schedule.index', $tournament));
});

Breadcrumbs::for('schedule.tables', function ($trail, $tournament) {
    $trail->push('Spielplan', route('schedule.tables', $tournament));
});

Breadcrumbs::for('schedule.show', function ($trail, $tournament, $championship) {
    $trail->push('Spielplan', route('schedule.show', [$tournament, $championship]));
});

Breadcrumbs::for('schedule.phase', function ($trail, $tournament, $championship, $phase) {
    $trail->push($phase->name(), route('schedule.phase', [$tournament, $championship, $phase]));
});

Breadcrumbs::for('login', function ($trail) {
    $trail->push('login', route('login'));
});
