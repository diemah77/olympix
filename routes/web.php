<?php

use Illuminate\Support\Facades\Route;

Route::get('login')->name('login')->uses('Auth\LoginController@showLoginForm')->middleware('guest');
Route::post('login')->name('login.attempt')->uses('Auth\LoginController@login')->middleware('guest');
Route::get('logout')->name('logout')->uses('Auth\LoginController@logout');

Route::get('/results/{tournament:hash}', 'ResultController')->name('results');
// Route::get('/results/{tournament:hash}', function(App\Tournament $tournament)
// {
//     $championship = $tournament->championships->first();
//     $phase = $championship->phases()->orderBy('order')->first();

//     return redirect()->route('results.show', [
//         'tournament' => $tournament,
//         'championship' => $championship,
//         'phase' => $phase]);
// })
// ->name('results');

Route::get('/results/{tournament:hash}/{championship:slug}', function(App\Tournament $tournament, App\Championship $championship)
{
    $phase = $championship->phases()->orderBy('order')->first();

    return redirect()->route('results.show', [
        'tournament' => $tournament,
        'championship' => $championship,
        'phase' => $phase]);
})
->name('results.championship');

Route::get('/results/{tournament:hash}/{championship:slug}/{phase:id}', 'ResultController@show')->name('results.show');

Route::get('/', function()
{
    return redirect()->route('tournaments.index');
})->name('home');

Route::group(['prefix' => 'tournaments', 'middleware' => 'auth'], function()
{
    Route::get('/', 'TournamentController@index')->name('tournaments.index');
    Route::get('/create', 'TournamentController@create')->name('tournaments.create');
    Route::post('/', 'TournamentController@store')->name('tournaments.store');
    Route::get('/{tournament}', 'TournamentController@edit')->name('tournaments.edit');
    Route::put('/{tournament}', 'TournamentController@update')->name('tournaments.update');
    Route::post('/{tournament}/publish', 'TournamentController@publish')->name('tournaments.publish');
    Route::get('/{tournament}/qr_code', 'TournamentController@downloadQRCode')->name('tournaments.qr_code');

    Route::group(['prefix' => '/{tournament}'], function()
    {
        Route::post('/tables', 'TournamentController@tables')->name('tables');
        Route::get('/tables', 'TableController@index')->name('tables.index');
        Route::post('/tables/rotate', 'TableController@rotate')->name('tables.rotate');

        Route::get('/players', 'PlayerController@index')->name('players.index');
        Route::get('/players/create', 'PlayerController@create')->name('players.create');
        Route::post('/players', 'PlayerController@store')->name('players.store');
        Route::get('/players/{player}', 'PlayerController@edit')->name('players.edit');
        Route::put('/players/{player}', 'PlayerController@update')->name('players.update');
        Route::post('/players/random', 'PlayerController@random')->name('players.random');
        Route::post('/players/import', 'PlayerController@import')->name('players.import');

        Route::get('/schedule', 'ScheduleController@index')->name('schedule.index');
        Route::get('/schedule/tables', 'ScheduleController@tables')->name('schedule.tables');
        Route::get('/schedule/{championship}', 'ScheduleController@show')->name('schedule.show');
        Route::get('/schedule/{championship}/phases/{phase}', 'ScheduleController@phase')->name('schedule.phase');
    });

    Route::group(['prefix' => '/{tournament}/championships'], function()
    {
        Route::get('/', 'ChampionshipController@index')->name('championships.index');
        Route::get('/create', 'ChampionshipController@create')->name('championships.create');
        Route::post('/', 'ChampionshipController@store')->name('championships.store');
        Route::get('/{championship}', 'ChampionshipController@edit')->name('championships.edit');
        Route::put('/{championship}', 'ChampionshipController@update')->name('championships.update');
        Route::delete('/{championship}', 'ChampionshipController@destroy')->name('championships.destroy');

        Route::group(['prefix' => '/{championship}'], function()
        {
            Route::post('/registrations/{player}', 'RegistrationController@store')->name('registrations.store');
            Route::post('/registrations', 'RegistrationController@storeAll')->name('registrations.store-all');
            Route::delete('/registrations/{player}', 'RegistrationController@destroy')->name('registrations.destroy');

            Route::put('/participants', 'ParticipantController@update')->name('participants.update');

            Route::get('/players', 'ChampionshipController@players')->name('championship-players');
            Route::get('/doubles', 'ChampionshipController@doubles')->name('championship-doubles');
            Route::get('/seeding', 'ChampionshipController@seeding')->name('championship-seeding');

            Route::post('/doubles', 'DoubleController@store')->name('doubles.store');
	        Route::delete('/doubles', 'DoubleController@destroy')->name('doubles.destroy');

            Route::get('/phases/{phase}', 'PhaseController@edit')->name('phases.edit');
            Route::post('/phases/{phase}', 'PhaseController@start')->name('phases.start');
            Route::post('/phases/{phase}/draws', 'DrawController@store')->name('draws.store');
            Route::delete('/phases/{phase}/draws', 'DrawController@destroy')->name('draws.destroy');

            Route::post('/matches/{match}/start', 'MatchController@start')->name('matches.start');
            Route::post('/matches/{match}/stop', 'MatchController@stop')->name('matches.stop');
        });
    });
});


