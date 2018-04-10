<?php

use Illuminate\Database\Seeder;

use App\Difficulty;

class DifficultiesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('difficulties')->delete();

        for ($range = self::MIN_RANGE; $range <= self::MAX_RANGE; $range++) {
            for ($maxNotes = self::MIN_NOTES; $maxNotes <= self::MAX_NOTES; $maxNotes++) {
                $difficulty = new Difficulty;

                $difficulty->range = $range;
                $difficulty->min_notes = self::MIN_NOTES;
                $difficulty->max_notes = $maxNotes;

                $difficulty->saveOrFail();
            }
        }
    }
}
