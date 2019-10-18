<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\API\RhythmExerciseController;
use App\Http\Controllers\Utils\Midi\MidiNotes;

class GenerateRhythmExercises extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:rhythm {level} {n}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates specified number of rhythm exercises for specified level (generate offline cache)';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $level = $this->argument('level');
        $n     = $this->argument('n');
        echo "AND selected level is $level. Will generate $n exercises\n";

        $exeriseController = new RhythmExerciseController();
        $soundController = new MidiNotes();

        $baseFilePath = "storage/midi/base";

        $info = (object) [
            'metronome' => true,
        ];

        for($i = 0; $i < $n; $i++) {
            echo ($i + 1) . " / $n...  exercise: ";
            $exId = $exeriseController->generateNew($level);
            echo "OK! ($exId)   sound: ";
            $sRes = $soundController->GenerateExerciseSound($exId, $baseFilePath.$exId, $info);
            echo "OK! (".json_encode($sRes).")\n";
        }
    }
}
