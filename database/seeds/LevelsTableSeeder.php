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

        foreach (self::LEVELS as $item) {
            $level = new Level;

            $level->level = $item['level'];
            $level->max_note_count = $item['max_note_count'];

            $level->saveOrFail();
        }
    }
}
