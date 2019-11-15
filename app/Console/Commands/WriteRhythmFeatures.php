<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Utils\MusicXML;

use Illuminate\Support\Facades\DB;

class WriteRhythmFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:WriteRhythmFeatures {file} {featurename} {rhythmlevel}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function mxlType($filename) {
        $fh = @fopen($filename, "r");

        if (!$fh) {
        print "ERROR: couldn't open file.\n";
        exit(126);
        }

        $blob = fgets($fh, 5);

        fclose($fh);

        if (strpos($blob, '<?') !== false) {
            return "MusicXML";
        } else if (strpos($blob, 'PK') !== false) {
            return "MXL (ZIP)";
        } else {
            return "I dunno.";
        }
    }

    private function _getBarLength($notes){

        $length = 0; $tupletLength = 0;
    
        for($i = 0; $i < count($notes); $i++){
            
            $note = $notes[$i];
            
            if($note->type === "bar"){
                
                
                continue;
            }
            
            $dur = 4/$note->value;
            if($note->dot){
                $dur = $dur * 1.5;
            }
    
            if($note->in_tuplet) {
    
                $tupletLength += $dur;
    
                if($note->tuplet_end) {
                    $length += $tupletLength/$note->tuplet_type->num_notes * $note->tuplet_type->in_space_of;
                    $tupletLength = 0;
                }
    
            }else {
                $length += $dur;
            }
            
            
        }

        if($tupletLength > 0 || $length->getDenominator() != 1) {
            return false;
        } else {
            return $length;
        }
    }

    private function _createRhythmFeature($name, $rhythm_level, $feature_probability, $bar_info_ids) {
        DB::statement("INSERT INTO rhythm_features (name, cross_bar) VALUES (?, 0)", [$name]);
        $fid = DB::getPdo()->lastInsertId();
        

        foreach($bar_info_ids as $bid){
            DB::statement("INSERT INTO rhythm_feature_occurrences (rhythm_level, rhythm_feature_id, bar_info_id, feature_probability) VALUES (?, 18, ? , 1)", [$rhythm_level, $bid]);
        }

        foreach($bar_info_ids as $bid){
            DB::statement("INSERT INTO rhythm_feature_occurrences (rhythm_level, rhythm_feature_id, bar_info_id, feature_probability)". 
            " VALUES (?, ?, ?, ?)", [
                $rhythm_level,
                $fid,
                $bid,
                $feature_probability
            ]);
        }

        return $fid;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $file = $this->argument('file');
        $featurename = $this->argument('featurename');
        $rhythmlevel = $this->argument('rhythmlevel');
        
        $jsonValues = json_decode(file_get_contents($file));

        $groupingBarInfos = [
            "1/4" => [
                1, // (4/4)
                2,  // (3/4)
                4, // (3/4+2/4)
                5, // (2/4+3/4)
            ],
            "3/8" => [
                3, // (6/8)
                6, // (6/8)
            ]
        ];


        DB::transaction(function() use($groupingBarInfos, $jsonValues, $rhythmlevel, $featurename) {

            foreach($jsonValues as $grouping => $values) {
                $gbis = $groupingBarInfos[$grouping];
                $fid = $this->_createRhythmFeature("$featurename ($grouping)", $rhythmlevel, 0.5, $gbis);
    
                // TODO TODO! TESTIRAJ!
                foreach($values as $barID => $prob) {
                    DB::statement("INSERT INTO rhythm_bar_occurrences (rhythm_bar_id, rhythm_feature_id, bar_probability) VALUES (?,?,?)",
                    [$barID, $fid, $prob]);
                }
            }

        });

        
        echo "\n\n";
    }
}
