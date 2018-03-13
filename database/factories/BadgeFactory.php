<?php

use Faker\Generator as Faker;

$factory->define(App\Badge::class, function (Faker $faker) {
    return [
        'name'        => $faker->word,
        'description' => $faker->sentence,
        'image'       => $faker->image('/storage/images')
    ];
});
