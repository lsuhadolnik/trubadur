<?php

use Faker\Generator as Faker;

$factory->define(App\Game::class, function (Faker $faker) {
    return [
        'winner_id' => function () {
            return factory(App\User::class)->create()->id;
        }
    ];
});
