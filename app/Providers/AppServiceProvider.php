<?php

namespace App\Providers;

use Breadcrumbs;
use App\Tournament;
use App\Championship;
use Inertia\Inertia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Model::unguard();

        Inertia::share([
            'auth' => function() {
                $user = auth()->user() ?? \App\User::first();

                return [
                    'avatar' => $user->avatar(),
                    'name' => $user->name,
                ];
            },
            // 'breadcrumbs' => function() {
            //     return Breadcrumbs::generate();
            // },
            't' => function() {
                $t = request()->route('tournament');
                $t = $t instanceof Tournament ? $t : Tournament::find($t);

                if ($t)
                {
                    $championships = $t->championships()->get();
                }

                return $t ? [
                    'id' => $t->id,
                    'name' => $t->name,
                    'championships' => $championships,
                    'started_championships_count' => $championships->filter(function ($c)
                    {
                        return $c->hasNonPreparedPhase();
                    })->count()
                ] : null;
            },

            'c' => function() {
                $c = request()->route('championship');
                $c = $c instanceof Championship ? $c : Championship::find($c);

                return $c ? [
                    'id' => $c->id,
                    'name' => $c->name,
                    'isDoubles' => $c->isDoubles()
                ] : null;
            }
        ]);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
