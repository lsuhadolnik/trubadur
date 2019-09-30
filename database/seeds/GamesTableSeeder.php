<?php

use Illuminate\Database\Seeder;

use App\Answer;
use App\Game;
use App\GameUser;
use App\Difficulty;
use App\Question;
use App\User;

class GamesTableSeeder extends DatabaseSeeder
{
    const GAME_MODES = ['practice', 'single', 'multi'];
    const GAME_TYPES = ['intervals', 'rhythm'];
    const INSTRUMENTS = ['clarinet', 'guitar', 'piano', 'trumpet', 'violin'];
    const N_CHAPTERS = 3;
    const N_QUESTIONS = 8;
    const PITCHES = ['A#3', 'B3', 'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4', 'C5', 'C#5'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('questions')->delete();
        DB::table('answers')->delete();
        DB::table('games')->delete();

        for ($i = 0; $i < self::N_GAMES; $i++) {
            $game = new Game;

            $difficulty = Difficulty::inRandomOrder()->first();
            $game->difficulty()->associate($difficulty);

            $game->mode = self::GAME_MODES[array_rand(self::GAME_MODES)];
            $game->type = self::GAME_TYPES[array_rand(self::GAME_TYPES)];

            $game->saveOrFail();

            $nUsers = rand(self::MIN_USERS_PER_GAME, self::MAX_USERS_PER_GAME);
            $users = User::inRandomOrder()->take($nUsers)->pluck('id');

            $questions = [];
            for ($j = 1; $j <= self::N_CHAPTERS; $j++) {
                for ($k = 1; $k <= self::N_QUESTIONS; $k++) {
                    $pitches = self::PITCHES;
                    shuffle($pitches);
                    $nNotes = rand($difficulty->min_notes, $difficulty->max_notes);
                    $content = implode(',', array_slice($pitches, 0, $nNotes));

                    $question = new Question;

                    $question->game()->associate($game);
                    $question->chapter = $j;
                    $question->number = $k;
                    $question->content = $content;

                    $question->saveOrFail();

                    $questions[] = $question;
                }
            }

            foreach ($users as $userId) {
                $instrument = self::INSTRUMENTS[array_rand(self::INSTRUMENTS)];
                $finished = (rand(0, 100) / 100) < 0.8;
                $game->users()->attach($userId, [
                    'instrument' => $instrument,
                    'points'     => $finished ? rand(0, 250) : 0,
                    'finished'   => $finished
                ]);

                foreach ($questions as $question) {
                    $answer = new Answer;

                    $answer->game_id = $game->id;
                    $answer->user_id = $userId;
                    $answer->question()->associate($question);
                    $answer->success = (rand(0, 100) / 100) < 0.8;
                    $answer->time = $answer->success ? rand(10000, 120000) : 120000;
                    $answer->n_additions = rand($difficulty->max_notes - 1, 10);
                    $answer->n_deletions = rand($answer->n_additions - 3, $answer->n_additions);
                    $answer->n_playbacks = rand(0, 5);
                    $answer->n_answers = rand(1, 5);

                    $answer->saveOrFail();
                }
            }

            $game->saveOrFail();
        }
    }
}
