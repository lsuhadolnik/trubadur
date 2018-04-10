<?php

use Illuminate\Database\Seeder;

use App\Grade;

class GradesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('grades')->delete();

        for ($n = self::MIN_GRADE; $n <= self::MAX_GRADE; $n++) {
            $grade = new Grade;

            $grade->grade = $n;

            $grade->saveOrFail();
        }
    }
}
