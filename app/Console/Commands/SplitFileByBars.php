<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Utils\MusicXML;

class SplitFileByBars extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:SplitFileByBars {file} {outFile} {barNums}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reads MusicXML stave lines from file, decomposes stavelines into separate files and saves them for future analysis';

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

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // Prvi program (TO JE TA)
        
        // Open file
        // Extract rhythm bars
            // Iz podanih števil N taktov zgradi N datotek z musisonom

        // Drugi program
            // Podano datoteko razbij na takte in ritmične vzorce
            // Izračunaj njihove pojavitve glede na vse vzorce iz istega taktovskega načina v datoteki
            // Shrani v JSON

        // Tretji program: vhod je JSON z vzorci in pogostostmi
            // Podano je ime rhythm_featurja in rhythm_level
            // Vsak vzorec shrani v podatkovno bazo
            // Dodaj rhythm_feature
            // Dodaj rhythm_feature_occurrence (za vsak nivo in takt. nač. po dva - enega še za cross_bar)



        $file = $this->argument('file');
        $outfile = $this->argument('outFile');
        $barNumsString = $this->argument('barNums');


        echo "AND the file is: $file\n";
        echo "It is of type ".$this->mxlType($file)." \n\n";

        $fileContent = file_get_contents($file);

        $xml = new \SimpleXMLElement($fileContent);
        $takti = MusicXML::parseMeasures($xml);

        echo "The file has ". count($takti) . " bars.";

        $barNums = explode(',', $barNumsString);
        
        $barOffset = 0;


        foreach($barNums as $bNum) {

            $parts = explode('|', $bNum);
            $numBeats = $parts[0];
            $level = $parts[1];

            $groups = [];
            $i = 0;
            for($i; $i < $numBeats && $barOffset + $i < count($takti); $i++) {
                
                $bar = (object) $takti[$i + $barOffset];
                if(!$bar) {
                    echo "🤬🤬🤬";
                }
                if(!array_key_exists($bar->barInfo, $groups)) {
                    $groups[$bar->barInfo] = [];
                }
    
                array_push($groups[$bar->barInfo], $bar->content);
                
                
            }
            $barOffset += $i - 1;

            

            $fn = "$outfile/$level".'_'."$numBeats.json";
            echo "Writing $fn  \n\n";
            file_put_contents($fn, json_encode($groups));
        }
        


        

        echo "\n\n";
    }
}
