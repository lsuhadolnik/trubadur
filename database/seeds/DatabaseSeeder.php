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
            'level'     => 'easy',
            'range'     => 5,
            'min_notes' => 4,
            'max_notes' => 6
        ],
        [
            'level'     => 'normal',
            'range'     => 12,
            'min_notes' => 4,
            'max_notes' => 6
        ],
        [
            'level'     => 'hard',
            'range'     => 12,
            'min_notes' => 4,
            'max_notes' => 8
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
            'name'                => 'Wolfgang Amadeus Mozart',
            'email'               => 'mozart@trubadur.si',
            'password'            => 'mozart123',
            'instrument'          => 'piano',
            'note_playback_delay' => 2000,
            'clef'                => 'violin',
            'school'              => 'Akademija za Glasbo',
            'grade'               => '2',
            'badges'              => ['All answers correct', '20 questions without mistake']
        ],
        [
            'name'                => 'Ludwig van Beethoven',
            'email'               => 'beethoven@trubadur.si',
            'password'            => 'beethoven123',
            'instrument'          => 'guitar',
            'note_playback_delay' => 1500,
            'clef'                => 'violin',
            'school'              => 'Konservatorij za glasbo in balet Ljubljana',
            'grade'               => '1',
            'badges'              => ['All answers correct']
        ],
        [
            'name'                => 'Johannes Brahms',
            'email'               => 'brahms@trubadur.si',
            'password'            => 'brahms123',
            'instrument'          => 'clarinet',
            'note_playback_delay' => 2500,
            'clef'                => 'bass',
            'school'              => 'Glasbena šola Mengeš',
            'grade'               => '3',
            'badges'              => []
        ],
        [
            'name'                => 'Antonio Vivaldi',
            'email'               => 'vivaldi@trubadur.si',
            'password'            => 'vivaldi123',
            'instrument'          => 'trumpet',
            'note_playback_delay' => 1000,
            'clef'                => 'violin',
            'school'              => 'Port Regis',
            'grade'               => '4',
            'badges'              => []
        ],
            [
            'name'                => 'Joseph Haydn',
            'email'               => 'haydn@trubadur.si',
            'password'            => 'haydn123',
            'instrument'          => 'violin',
            'note_playback_delay' => 500,
            'clef'                => 'bass',
            'school'              => 'Professional Performing Arts High School',
            'grade'               => '2',
            'badges'              => ['20 questions without mistake']
        ]
    ];

    const N_GAMES = 10;
    const MIN_USERS_PER_GAME = 1;
    const MAX_USERS_PER_GAME = 4;

    const MIN_LOGINS_PER_USER = 10;
    const MAX_LOGINS_PER_USER = 50;

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
        $this->call(LoginsTableSeeder::class);
    }
}
