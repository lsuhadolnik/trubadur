<?php

use Illuminate\Database\Seeder;

use App\Game;
use App\User;

class GamesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('games')->delete();

        for ($i = 0; $i < self::N_GAMES; $i++) {
            $nUsers = rand(self::MIN_USERS_PER_GAME, self::MAX_USERS_PER_GAME);
            $users = User::inRandomOrder()->take($nUsers)->pluck('id');

            $game = new Game;
            $game->saveOrFail();

            $game->users()->attach($users);

            $winner = User::find($users[rand(0, $nUsers - 1)]);
            $game->winner()->associate($winner);
            $game->saveOrFail();
        }
    }
}
