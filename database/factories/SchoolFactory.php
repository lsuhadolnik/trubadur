<?php

use Faker\Generator as Faker;

$factory->define(App\School::class, function (Faker $faker) {
    return [
        'name'       => $faker->company,
        'type'       => $faker->randomElement(['primary' ,'high', 'university']),
        'country_id' => function () {
            return factory(App\Country::class)->create()->id;
        }
    ];
});
