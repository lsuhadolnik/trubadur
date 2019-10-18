<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class ClearRhythmExerciseCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:rhythm:clear {level}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears rhythm execise cache';

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
        echo "AND selected level is $level. Deleteing cache...\n";

        $basePath = 'storage/midi/base';
        $extensions = ['mid', 'wav', 'mp3'];

        $exercises = DB::select(
            'SELECT id from rhythm_exercises where mp3_generated = 1 && rhythm_level = ?', 
            [$level]);

        echo "Found ".count($exercises)." exercises\n";

        foreach($exercises as $ex) {

            $exId = $ex->id;
            echo "EX: $exId:  ";

            foreach($extensions as $ext) {
                // Check if files exist
                $filename = $basePath.$exId.'.'.$ext;
                if(file_exists($filename)) {
                    echo "Deleting $filename... ";
                    // Delete
                    unlink($filename);
                }
            }
            
            echo "Clearing flag for $exId... ";
            // Clear the flag
            $ch = DB::statement("UPDATE rhythm_exercises SET mp3_generated = 0 WHERE id = ?", [$exId]);
            echo "Changed $ch rows\n";
        }

    }
}
