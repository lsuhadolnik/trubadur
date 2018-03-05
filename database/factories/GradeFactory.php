<?php

use Faker\Generator as Faker;

$factory->define(App\Grade::class, function (Faker $faker) {
    return [
        'grade' => rand(1, 4)
    ];
});
