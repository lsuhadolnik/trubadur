<?php

use Faker\Generator as Faker;

$factory->define(App\User::class, function (Faker $faker) {
    static $password;

    return [
        'name'                => $faker->name,
        'email'               => $faker->unique()->safeEmail,
        'password'            => $password ?: $password = bcrypt('secret'),
        'remember_token'      => str_random(10),
        'instrument'          => $faker->randomElement(['acoustic_grand_piano', 'violin', 'vibraphone', 'trumpet', 'cello']),
        'note_playback_delay' => rand(500, 2500),
        'clef'                => $faker->randomElement(['violin', 'bass']),
        'rating'              => rand(500, 1500),
        'school_id'           => function () {
            return factory(App\School::class)->create()->id;
        },
        'grade_id'            => function () {
            return factory(App\Grade::class)->create()->id;
        },
    ];
});

$factory->state(App\User::class, 'test', function (Faker $faker) {
    return [
        'school_id' => function () {
            return factory(App\School::class)->make()->id;
        },
        'grade_id'  => function () {
            return factory(App\Grade::class)->make()->id;
        }
    ];
});
