<?php

use Faker\Generator as Faker;

$factory->define(App\Level::class, function (Faker $faker) {
    $index = rand(0, 2);

    return [
        'level' => ['easy', 'normal', 'hard'][$index],
        'max_note_count' => [5, 8, 12][$index]
    ];
});
