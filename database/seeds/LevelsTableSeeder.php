<?php

use Illuminate\Database\Seeder;

use App\Level;

class LevelsTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('levels')->delete();

        $currentRating = 0;
        for ($n = 1; $n <= 45; $n++) {
            $level = new Level;

            $level->level = $n;
            if ($n <= 5) {
                $factor = 250;
            } else if ($n > 5 && $n <= 10) {
                $factor = 500;
            } else if ($n > 10 && $n <= 15) {
                $factor = 1000;
            } else if ($n > 15 && $n <= 20) {
                $factor = 2000;
            } else if ($n > 20 && $n <= 30) {
                $factor = 3000;
            } else if ($n > 30 && $n <= 40) {
                $factor = 4000;
            } else {
                $factor = 5000;
            }

            if (array_key_exists($n, self::LEVELS)) {
                $level->label = self::LEVELS[$n];
                $level->image = '/images/levels/default.svg';
            }
            $level->min_rating = $currentRating;
            $level->max_rating = $currentRating + $factor - 1;

            $level->saveOrFail();

            $currentRating += $factor;
        }
    }
}
