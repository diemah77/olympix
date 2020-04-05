<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersSeeder::class);
        $this->call(ChampionshipSystemsSeeder::class);
        $this->call(ChampionshipTypesSeeder::class);
        $this->call(ResultSeeder::class);
        $this->call(QualifiedSeeder::class);

        if (app()->environment() == 'local')
        {
            $this->call(TournamentSeeder::class);
        }
    }
}

class UsersSeeder extends Seeder
{
    public function run()
    {
    	App\User::create([
    		'name' => 'Dieter',
            'email' => 'dfrei84@gmail.com',
            'password' => bcrypt('viktoria'),
        ]);

        App\User::create([
    		'name' => 'Malle',
            'email' => 'schriftfuehrer@ttc-olympia.de',
            'password' => bcrypt('olaympia'),
        ]);

        App\User::create([
    		'name' => 'Jens',
            'email' => 'jens.koch06@gmail.com',
            'password' => bcrypt('müllem'),
        ]);

        App\User::create([
    		'name' => 'Luke',
            'email' => 'kommunikation@ttc-olympia.de',
            'password' => bcrypt('olympia'),
        ]);

        App\User::create([
    		'name' => 'Uli',
            'email' => 'uli_manner@yahoo.de',
            'password' => bcrypt('lay_uli'),
        ]);

        App\User::create([
    		'name' => 'Thomas',
            'email' => 'thomas.berek@web.de',
            'password' => bcrypt('lay_thomas'),
        ]);
    }
}

class ChampionshipSystemsSeeder extends Seeder
{
    public function run()
    {
        $systems = ['Einfach-KO', 'Doppel-KO', 'Gruppen/KO'];

        foreach($systems as $system)
        {
            App\ChampionshipSystem::create([
                'name' => $system,
            ]);
        }
    }
}

class ChampionshipTypesSeeder extends Seeder
{
    public function run()
    {
        $types = ['Einzel', 'Doppel'];

        foreach($types as $type)
        {
            App\ChampionshipType::create([
                'name' => $type,
            ]);
        }
    }
}

class QualifiedSeeder extends Seeder {

    public function run()
    {
        DB::table('qualifieds')->insert([
            ['value' => 1, 'name' => 'Gruppensieger', 'extra' => false],
            ['value' => 2, 'name' => 'Platz 1-2', 'extra' => false],
            ['value' => 3, 'name' => 'Platz 1-3', 'extra' => false],
            ['value' => 4, 'name' => 'Platz 1-4', 'extra' => false],
            ['value' => 1, 'name' => 'Bester vom Rest', 'extra' => true],
            ['value' => 2, 'name' => 'Beste zwei vom Rest', 'extra' => true],
        ]);
    }
}

class ResultSeeder extends Seeder {

    public function run()
    {
        DB::table('results')->insert([
            ['left' => 3, 'right' => 0, 'size' => 5],
            ['left' => 3, 'right' => 1, 'size' => 5],
            ['left' => 3, 'right' => 2, 'size' => 5],
            ['left' => 2, 'right' => 3, 'size' => 5],
            ['left' => 1, 'right' => 3, 'size' => 5],
            ['left' => 0, 'right' => 3, 'size' => 5],
            ['left' => 4, 'right' => 0, 'size' => 7],
            ['left' => 4, 'right' => 1, 'size' => 7],
            ['left' => 4, 'right' => 2, 'size' => 7],
            ['left' => 4, 'right' => 3, 'size' => 7],
            ['left' => 3, 'right' => 4, 'size' => 7],
            ['left' => 2, 'right' => 4, 'size' => 7],
            ['left' => 1, 'right' => 4, 'size' => 7],
            ['left' => 0, 'right' => 4, 'size' => 7],
        ]);
    }
}

class TournamentSeeder extends Seeder {

    public function run()
    {
        $u = App\User::first();
        $t = factory(App\Tournament::class)->create(['name' => 'Pfannenturnier', 'user_id' => $u->id]);
        $players = factory(App\Player::class, 16)->create(['tournament_id' => $t->id]);
        $c = factory(App\Championship::class)->create([
            'tournament_id' => $t->id,
            'name' => 'Einzel',
            'system_id' => 1,
            'third_place' => false,
            'type_id' => 1,
            'sets' => 5
        ]);

        foreach($players as $player)
        {
            $c->register($player);
        }
    }
}

