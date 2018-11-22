<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    const COUNTRIES = [
        'Hrvaška', 'Slovenija', 'Velika Britanija', 'Združene države Amerike'
    ];

    const BADGES = [
        [
            'name'        => 'Igra brez napake',
            'description' => 'Pravilno moraš odgovoriti na vsa vprašanja v igri.',
            'image'       => 'star'
        ],
        [
            'name'        => 'Igra s 50% točnostjo',
            'description' => 'Pravilno moraš odgovoriti na vsaj 50% vprašanj v igri.',
            'image'       => 'star'
        ],
        [
            'name'        => 'Igra končana v 25 minutah',
            'description' => 'Igro moraš končati v času izpod 25 minut.',
            'image'       => 'star'
        ],
        [
            'name'        => 'Dokončana igra 3 dni zapored',
            'description' => '3 dni zapored moraš dokončati igro.',
            'image'       => 'trophy'
        ],
        [
            'name'        => 'Dokončana igra 7 dni zapored',
            'description' => '7 dni zapored moraš dokončati igro.',
            'image'       => 'trophy'
        ],
        [
            'name'        => 'Dokončana igra z vsemi različnimi inštrumenti',
            'description' => 'Dokončati moraš vsaj 1 igro z vsakim od inštrumentov.',
            'image'       => 'trophy'
        ],
        [
            'name'        => 'Prijava 3 dni zapored',
            'description' => '3 dni zapored se moraš prijaviti v aplikacijo.',
            'image'       => 'flag'
        ],
        [
            'name'        => 'Prijava 7 dni zapored',
            'description' => '7 dni zapored moraš dokončati igro.',
            'image'       => 'flag'
        ],
        [
            'name'        => 'Zmaga v večigralski igri',
            'description' => 'Zmagati moraš v igri večigralskega načina.',
            'image'       => 'crown'
        ]

    ];

    const GRADES = [
        'primary'    => 6,
        'high'       => 4,
        'university' => 5
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
            'name'    => 'Glasbena šola',
            'type'    => 'primary',
            'country' => 'Slovenija'
        ]
    ];

    const USERS = [
        [
            'name'                => 'Wolfgang Amadeus Mozart',
            'email'               => 'mozart@trubadur.si',
            'password'            => 'mozart123',
            'instrument'          => 'guitar',
            'note_playback_delay' => 2500,
            'clef'                => 'violin',
            'school'              => 'Konservatorij za glasbo in balet Ljubljana',
            'grade'               => '2',
            'badges'              => ['Igra s 50% točnostjo', 'Zmaga v večigralski igri']
        ],
        [
            'name'                => 'Ludwig van Beethoven',
            'email'               => 'beethoven@trubadur.si',
            'password'            => 'beethoven123',
            'instrument'          => 'piano',
            'note_playback_delay' => 2000,
            'clef'                => 'violin',
            'school'              => 'Konservatorij za glasbo in balet Ljubljana',
            'grade'               => '1',
            'badges'              => ['Igra končana v 25 minutah', 'Zmaga v večigralski igri']
        ],
        [
            'name'                => 'Johannes Brahms',
            'email'               => 'brahms@trubadur.si',
            'password'            => 'brahms123',
            'instrument'          => 'clarinet',
            'note_playback_delay' => 3000,
            'clef'                => 'violin',
            'school'              => 'Glasbena šola',
            'grade'               => '5',
            'badges'              => ['Igra s 50% točnostjo']
        ]
    ];

    const LEVELS = [
        5  => 'Recital za Glasbeno Mladino v Cankarjevem domu',
        10 => 'Nastop s Komornim godalnim orkestrom Slovenske Filharmonije',
        15 => 'Snemanje v studiih RTV Slovenija',
        20 => 'Solo nastop z Orkestrom Slovenske Filharmonije',
        25 => 'Sodelovanje v Evropskem mladinskem orkestru',
        30 => 'Solo nastop v koncertni dvorani Concertgebow v Amsterdamu',
        35 => 'Igranje na evropski turneji s koncertno agencijo European Artistic Services',
        40 => 'Finale Evropskega tekmovanja mladih glasbenikov',
        45 => 'Zmaga na Evropskem tekmovanju mladih glasbenikov'
    ];

    const MIN_GRADE = 1;
    const MAX_GRADE = 6;

    const MIN_RANGE = 1;
    const MAX_RANGE = 12;
    const MIN_NOTES = 4;
    const MAX_NOTES = 8;

    const N_GAMES = 50;
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
        $this->call(DifficultiesTableSeeder::class);
        $this->call(SchoolsTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(GamesTableSeeder::class);
        $this->call(LoginsTableSeeder::class);
        $this->call(LevelsTableSeeder::class);
    }
}
