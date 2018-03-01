<?php

use Illuminate\Database\Seeder;

use App\Badge;
use App\Grade;
use App\School;
use App\User;

class UsersTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        foreach (self::USERS as $item) {
            $user = new User;

            $user->name = $item['name'];
            $user->email = $item['email'];
            $user->password = bcrypt($item['password']);

            $school = School::whereName($item['school'])->first();
            $user->school()->associate($school);

            $grade = Grade::whereGrade($item['grade'])->first();
            $user->grade()->associate($grade);

            $user->saveOrFail();

            foreach ($item['badges'] as $badgeName) {
                $badge = Badge::whereName($badgeName)->first();
                $user->badges()->attach($badge->id);
            }
        }
    }
}
