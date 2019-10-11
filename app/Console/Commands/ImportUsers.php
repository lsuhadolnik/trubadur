<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\User;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports CSV file of users and returns a new csv with IDs';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private function generatePass() {
        $pass = "";
        for($i = 0; $i < 3; $i++){
            $pass .= chr(rand(65, 90));
        }

        for($i = 0; $i < 3; $i++){
            $pass .= rand(0, 9);
        }

        return $pass;
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $filename = "users.csv";
        $outFilename = "usersFilledData.csv";

        // inHeaders: $users = ['name', 'email', 'grade'];
        
        $outHeaders = ['id', 'name', 'email', 'grade', 'pass'];

        $users = [];

        $csvFile = file($filename);
        $lineIdx = -1;
        foreach ($csvFile as $line) {

            $lineIdx++;
            if($lineIdx == 0) {
                continue;
            }
            
            $data = str_getcsv($line, ';');
            
            $name = $data[0];
            $email = $data[1];
            $grade = $data[2];

            $pass = $this->generatePass();

            try {
                $u = User::create([
                    'name' => $name,
                    'email' => $email,
                    'password' => bcrypt($pass),
                    'verified' => 1,
                    'school_id' => 2,
                    'grade_id' => $grade
                ]);
    
                $users[] = [$u->id, $name, $email, $grade, $pass];

            } catch(\Exception $e) {
                echo "USER CREATE FAILED: ".$e->getMessage()."\n";
            }

            
        }

        $fp = fopen($outFilename, 'w');
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));

        fputcsv($fp, $outHeaders, ';');
        foreach ($users as $fields) {
            fputcsv($fp, $fields, ';');
        }

        fclose($fp);
    }
}
