<?php

use Faker\Generator as Faker;

$factory->define(App\Level::class, function (Faker $faker) {
    $index = rand(0, 2);

    return [
        'level'     => ['easy', 'normal', 'hard'][$index],
        'range'     => [5, 12, 12][$index],
        'min_notes' => 4,
        'max_notes' => [4, 8, 10][$index]
    ];
});
