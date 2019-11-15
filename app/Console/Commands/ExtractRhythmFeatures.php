<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Utils\MusicXML;

use Illuminate\Support\Facades\DB;

use Phospr\Fraction;

class ExtractRhythmFeatures extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:ExtractRhythmFeatures {file} {outfile}';

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

        $length = new Fraction(0); $tupletLength = new Fraction(0);
    
        for($i = 0; $i < count($notes); $i++){
            
            $note = $notes[$i];
            
            if($note->type === "bar"){
                continue;
            }
            
            $dur = new Fraction(1, $note->value);
            if(isset($note->dot) && $note->dot){
                $dur = $dur->multiply(new Fraction(3,2));
            }
    
            if(isset($note->in_tuplet) && $note->in_tuplet) {
    
                $tupletLength = $tupletLength->add($dur);
    
                if(isset($note->tuplet_end) && $note->tuplet_end) {
                    $length = $length->add($tupletLength->multiply(new Fraction($note->tuplet_type->in_space_of, $note->tuplet_type->num_notes)));
                    $tupletLength = new Fraction(0);
                }
    
            }else {
                $length = $length->add($dur);
            }
            
            
        }

        if($tupletLength > new Fraction(0)) {
            return new Fraction(-1,1);
        } else {
            return $length;
        }
    }

    private function _dividesEvenly(Fraction $f1, Fraction $f2) {
        return $f1->divide($f2)->isInteger();
    }

    private function _findInDB($notes) {
        return DB::select("SELECT id from rhythm_bars where content = CAST(? as JSON) ORDER BY id LIMIT 1", [json_encode($notes)]);
    }

    private function _getBarLengthNum($notes, $stopOnBar){

        $length = 0;
        $tupletLength = 0;
    
        for($i = 0; $i < count($notes); $i++){
            
            $note = $notes[$i];
            
            if($note->type == "bar"){
                if($stopOnBar){
                    return $length;
                }
                
                continue;
            }
            
            $dur = 4/$note->value;
            if(isset($note->dot) && $note->dot){
                $dur = $dur  *1.5;
            }
    
            if(isset($note->in_tuplet) && $note->in_tuplet){
    
                $tupletLength += $dur;
    
                if(isset($note->tuplet_end)){
                    $length += $tupletLength/$note->tuplet_type->num_notes*$note->tuplet_type->in_space_of;
                    $tupletLength = 0;
                }
    
            }else {
                $length += $dur;
            }
            
            
        }
    
        return $length;
    }
    
    private function _get_bar_length_properties($notes) {
    
        $length = $this->_getBarLengthNum($notes, false);
        $cross_bar = $this->_getBarLengthNum($notes, true);
        if($length > $cross_bar){
            return (object)[ "cross_bar" => $cross_bar, "length" => $length ];
        }
    
        return (object)["length" => $length, "cross_bar" => 0];
    
    }

    private function createBar($notes) {
        
        $lengths = $this->_get_bar_length_properties($notes);
        
        DB::statement("INSERT INTO rhythm_bars (content, length, cross_bar) VALUES (?, ?, ?)", [
            json_encode($notes),
            $lengths->length,
            $lengths->cross_bar,
        ]);

        return DB::getPdo()->lastInsertId();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $file = $this->argument('file');
        $outfile = $this->argument('outfile');
        $jsonContent = json_decode(file_get_contents($file));

        $barGroupings = [
            "4/4" => new Fraction(1, 4),
            "5/4" => new Fraction(1, 4),
            "3/4" => new Fraction(1, 4),
            "2/4" => new Fraction(1, 4),

            "3/8" => new Fraction(3, 8),
            "6/8" => new Fraction(3, 8),
            "9/8" => new Fraction(3, 8)
        ];

        $cutRules = [
            /*function($notes, $i, $grouping, $rhythmFigure) { 
                if(count($notes) <= $i) {
                    return true;
                } else {
                    return false;
                }
            },*/
            function($notes, $i, $grouping, $rhythmFigure) { 
                /* if current note is tuplet, do not cut */ 
                if(isset($notes[$i]->in_tuplet) && $notes[$i]->in_tuplet && !isset($notes[$i]->tuplet_end)) {
                    return false;
                } else {
                    return true;
                }
            },
            function($notes, $i, $grouping, $rhythmFigure) { 
                /* if next note has tie, do not cut. */ 
                if(count($notes) <= $i + 1) {
                    return true;
                }
                if(!(isset($notes[$i + 1]->tie) && $notes[$i + 1]->tie)) {
                    return true;
                } else {
                    return false;
                }
            },
            function($notes, $i, $grouping, $rhythmFigure) { 
                /* if current duration divides evenly with currentBarGrouping */ 
                $len = $this->_getBarLength($rhythmFigure);
                if($len <= new Fraction(0)) {
                    return false;
                }
                
                if($this->_dividesEvenly(
                    $this->_getBarLength($rhythmFigure),
                    $grouping
                )) {
                    return true;
                } else {
                    return false;
                }
            },
            
        ];

        $numElInLine = 0;

        $groupFeatures = [];

        foreach($jsonContent as $barInfoString => $content) {

            $barInfo = json_decode($barInfoString);
            $barInfoShort = $barInfo->num_beats . '/' . $barInfo->base_note;

            $grouping = $barGroupings[$barInfoShort];
            $groupingShort = $grouping->getNumerator() . '/' . $grouping->getDenominator();

            foreach($content as $notesString){
                $notes = json_decode($notesString);

                $rhythmFigure = [];
                if(!isset($groupFeatures[$groupingShort])){
                    $groupFeatures[$groupingShort] = [];
                }
    
                $figures = [];
                
                for($i = 0; $i < count($notes); $i++) {
                    $note = $notes[$i];
                    $rhythmFigure[] = $note;
    
                    $acc = true;
                    for($rID = 0; $rID < count($cutRules) && $acc; $rID++){
                        $rule = $cutRules[$rID];
                        $acc  = $acc && $rule($notes, $i, $grouping, $rhythmFigure);
                    }
                    if($acc) {
                        if(isset($rhythmFigure[0]) && isset($rhythmFigure[0]->tie)){
                            unset($rhythmFigure[0]->tie);
                        }

                        //$figures[] = $rhythmFigure;
                        $dbIDs = $this->_findInDB($rhythmFigure);
                        if(count($dbIDs) == 0) {
                            // new
                            $id = $this->createBar($rhythmFigure);
                            $groupFeatures[$groupingShort][] = $id;
                            echo "$id, ";
                        } else {
                            $dbID = $dbIDs[0];
                            $groupFeatures[$groupingShort][] = $dbID->id;
                            echo $dbID->id . ', ';
                        }
                        $numElInLine++;
                        if($numElInLine >= 10) {
                            echo "\n";
                            $numElInLine = 0;
                        }
                        $rhythmFigure = [];
                    }
                }
            }


        }

        $counts = [];
        foreach($groupFeatures as $grouping => $indices) {
            if(!isset($counts[$grouping])) {
                $counts[$grouping] = [];
            }

            $total = 0;

            foreach($indices as $id) {
                if(!isset($counts[$grouping][$id])){
                    $counts[$grouping][$id] = 0;
                }

                $counts[$grouping][$id]++;
                $total++;

            }

            foreach($counts[$grouping] as $id => $value) {
                $counts[$grouping][$id] = $value / $total;
            }
        }

        echo "\n\n";
        echo "Writing to $outfile\n\n";
        file_put_contents($outfile, json_encode($counts));


    
        // Situacija: imaš musison datoteke, kjer je material ločen po nivojih
        // Razbij datoteke po ritmičnih vzorcih in vzorce spravi v bazo
        // Za vsak taktovski način naredi svoj excel, kjer izračunaš pogostost ritmičnega elementa
        // Datoteka: <original_file_name>_BI<bar_info_id>.csv
        // Stolpci: rhythm_bar_id, bar_probability
        
        // Potem napiši še funkcijo, ki iz CSV v bazo vnese nov feature za izbran level, bar_info_id in feature name ali id

        // Ponavljaj
        // Dodaj noto
        // Preveri cutRules z and operatorjem: če se vsi strinjajo, potem naredi nov ritmični vzorec
        //      - to je nov ritmični vzorec in ga spravi v bazo
        //      - dodaj 1 k številu taktov
        //      - v tabeli z rezultati za določen taktovski način prištej 1
        // Deli vse števce s številom vseh vzorcev, da dobiš probability
        // Zapiši vsak podarray v svojo datoteko

        

        echo "\n\n";
    }
}
