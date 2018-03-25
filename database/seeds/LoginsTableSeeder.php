<?php

use Illuminate\Database\Seeder;

use App\Login;
use App\User;

class LoginsTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('logins')->delete();

        foreach (self::USERS as $user) {
            $user = User::whereName($user['name'])->first();

            $nLogins = rand(self::MIN_LOGINS_PER_USER, self::MAX_LOGINS_PER_USER);

            for ($i = 0; $i < $nLogins; $i++) {
                $login = new Login;

                $login->user()->associate($user);

                $login->saveOrFail();
            }
        }
    }
}
