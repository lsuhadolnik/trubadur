<?php

use Illuminate\Database\Seeder;

use App\Badge;

class BadgesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('badges')->delete();

        foreach (self::BADGES as $item) {
            $badge = new Badge;

            $badge->name = $item['name'];
            $badge->description = $item['description'];
            $badge->image = '/images/badges/' . $item['image'] . '.png';

            $badge->saveOrFail();
        }
    }
}
