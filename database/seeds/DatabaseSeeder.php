<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const COUNTRIES = [
        'Hrvaška', 'Slovenija', 'Velika Britanija', 'Združene države Amerike'
    ];

    const BADGES = [
        [
            'name'        => 'All answers correct',
            'description' => 'You need to answer correctly to all the questions in a single game.',
            'image'       => 'trophy'
        ],
        [
            'name'        => '20 questions without mistake',
            'description' => 'You need to answer correctly to 20 consecutive questions.',
            'image'       => 'owl'
        ]
    ];

    const GRADES = [
        1, 2, 3, 4
    ];

    const LEVELS = [
        [
            'level'          => 'easy',
            'max_note_count' => 5
        ],
        [
            'level'          => 'normal',
            'max_note_count' => 8
        ],
        [
            'level'          => 'hard',
            'max_note_count' => 12
        ]
    ];

    const SCHOOLS = [
        [
            'name'    => 'Akademija za Glasbo',
            'type'    => 'university',
            'country' => 'Slovenija'
        ],
        [
            'name'    => 'Konservatorij za glasbo in balet Ljubljana',
            'type'    => 'high',
            'country' => 'Slovenija'
        ],
        [
            'name'    => 'Glasbena šola Mengeš',
            'type'    => 'primary',
            'country' => 'Slovenija'
        ],
        [
            'name'    => 'Port Regis',
            'type'    => 'primary',
            'country' => 'Velika Britanija'
        ],
        [
            'name'    => 'Professional Performing Arts High School',
            'type'    => 'high',
            'country' => 'Združene države Amerike'
        ]
    ];

    const USERS = [
        [
            'name'     => 'Wolfgang Amadeus Mozart',
            'email'    => 'mozart@trubadur.si',
            'password' => 'mozart123',
            'school'   => 'Akademija za Glasbo',
            'grade'    => '2',
            'badges'   => ['All answers correct', '20 questions without mistake']
        ],
        [
            'name'     => 'Ludwig van Beethoven',
            'email'    => 'beethoven@trubadur.si',
            'password' => 'beethoven123',
            'school'   => 'Konservatorij za glasbo in balet Ljubljana',
            'grade'    => '1',
            'badges'   => ['All answers correct']
        ],
        [
            'name'     => 'Johannes Brahms',
            'email'    => 'brahms@trubadur.si',
            'password' => 'brahms123',
            'school'   => 'Glasbena šola Mengeš',
            'grade'    => '3',
            'badges'   => []
        ],
        [
            'name'     => 'Antonio Vivaldi',
            'email'    => 'vivaldi@trubadur.si',
            'password' => 'vivaldi123',
            'school'   => 'Port Regis',
            'grade'    => '4',
            'badges'   => []
        ],
        [
            'name'     => 'Joseph Haydn',
            'email'    => 'haydn@trubadur.si',
            'password' => 'haydn123',
            'school'   => 'Professional Performing Arts High School',
            'grade'    => '2',
            'badges'   => ['20 questions without mistake']
        ]
    ];

    const N_GAMES = 10;
    const MIN_USERS_PER_GAME = 1;
    const MAX_USERS_PER_GAME = 4;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountriesTableSeeder::class);
        $this->call(BadgesTableSeeder::class);
        $this->call(GradesTableSeeder::class);
        $this->call(LevelsTableSeeder::class);
        $this->call(SchoolsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GamesTableSeeder::class);
    }
}
