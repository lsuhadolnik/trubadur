<?php

use Illuminate\Database\Seeder;

use App\Country;
use App\Difficulty;
use App\Grade;
use App\School;

class SchoolsTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('schools')->delete();

        foreach (self::SCHOOLS as $item) {
            $school = new School;

            $school->name = $item['name'];
            $school->type = $item['type'];

            $country = Country::whereName($item['country'])->first();
            $school->country()->associate($country);

            $school->saveOrFail();

            $nGrades = self::GRADES[$school->type];

            for ($n = self::MIN_GRADE; $n <= $nGrades; $n++) {
                switch ($school->type) {
                    case 'primary':
                        $range = 3;
                        $maxNotes = 4;
                        break;
                    case 'high':
                        switch ($n) {
                            case 1:
                                $range = 5;
                                $maxNotes = 6;
                                break;
                            case 2:
                                $range = 12;
                                $maxNotes = 6;
                                break;
                            case 3:
                            case 4:
                                $range = 12;
                                $maxNotes = 8;
                                break;
                        }
                        break;
                    case 'university':
                        $range = 12;
                        $maxNotes = 8;
                        break;
                }
                $grade = Grade::whereGrade($n)->first();
                $difficulty = Difficulty::where(['range' => $range, 'max_notes' => $maxNotes])->first();
                $school->grades()->attach($grade->id, ['difficulty_id' => $difficulty->id]);
            }
        }
    }
}
