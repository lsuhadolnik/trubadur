<?php

use Illuminate\Database\Seeder;

use App\Country;
use App\Grade;
use App\Level;
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

            foreach (self::GRADES as $grade) {
                switch ($school->type) {
                    case 'primary':
                        $level = 'easy';
                        break;
                    case 'high':
                        switch ($grade) {
                            case 1:
                                $level = 'easy';
                                break;
                            case 2:
                                $level = 'normal';
                                break;
                            case 3:
                            case 4:
                                $level = 'hard';
                                break;
                        }
                        break;
                    case 'university':
                        $level = 'hard';
                        break;
                    default:
                        $level = 'normal';
                }
                $grade = Grade::whereGrade($grade)->first();
                $level = Level::whereLevel($level)->first();
                $school->grades()->attach($grade->id, ['level_id' => $level->id]);
            }
        }
    }
}
