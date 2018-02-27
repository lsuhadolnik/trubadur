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

        foreach (self::GRADES as $item) {
            $grade = new Grade;

            $grade->grade = $item;

            $grade->saveOrFail();
        }
    }
}
