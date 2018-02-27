<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const BADGES = [
        [
            'name'        => 'All answers correct',
            'description' => 'You need to answer correctly to all the questions in a single game.'
            'image'       => 'trophy'
        ],
        [
            'name'        => '20 questions without mistake',
            'description' => 'You need to answer correctly to 20 consecutive questions.'
            'image'       => 'owl'
        ]
    ];

    const COUNTRIES = [
        'Hrvaška', 'Slovenija', 'Velika Britanija', 'Združene države Amerike'
    ];

    const GRADES = [
        1, 2, 3, 4
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
            'name'    => 'Osnovna šola Mengeš',
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
        ],
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
