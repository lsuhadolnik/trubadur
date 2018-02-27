<?php

use Illuminate\Database\Seeder;

use App\Country;

class CountriesTableSeeder extends DatabaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('countries')->delete();

        foreach (self::COUNTRIES as $item) {
            $country = new Country;

            $country->name = $item;

            $country->saveOrFail();
        }
    }
}
