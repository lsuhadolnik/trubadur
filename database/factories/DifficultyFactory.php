<?php

use Faker\Generator as Faker;

$factory->define(App\Difficulty::class, function (Faker $faker) {
    return [
        'range'     => rand(1, 12),
        'min_notes' => 4,
        'max_notes' => rand(4, 8)
    ];
});
