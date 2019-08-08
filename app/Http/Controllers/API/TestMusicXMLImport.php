<?php

    

    
    function packMeasure(&$m, $barInfo) {
        unset($barInfo["divisions"]);
        return array(
            "content" => json_encode($m),
            "barInfo" => json_encode($barInfo)
        );
    }

    function fillNoteValue($barInfo, &$note, $data){
        // Value
        $typeMap = array(
            "whole" => 1, "half" => 2, "quarter" => 4,
            "eighth" => 8, "16th" => 16, "32nd" => 32,
            "64th" => 64, "128th" => 128
        );


        if(array_key_exists('type', $data)){
            
            $type = (string) $data->type;
            if(array_key_exists($type, $typeMap)){
                
                $note['value'] = $typeMap[$type];

            }else{
                throw new \Exception("Note type ".$type." is so weird it does not map to any value...");
            }
            
        } /*else if(array_key_exists('duration', $data)){

            // Get using barInfo
            $divisions = $barInfo['divisions'];
            $duration = (int) $data->duration;

            // Če je četrtinka razdeljena na 8 kosov in traja nota 4 kose, je to osminka (8)
            //  = $duration / $divisions = 4 / 8 = 0.5 četrtinke = $value
            //  Nota
            //  Celinka   = 4                
            //  Polovinka = 2
            //  Četrtinka = 1   = 1 / 24  (tri note v času dveh) 1 * 3 / 2
            //  Osminka   = 1/2
            //  Šestnajstinka = 1/4
            //  Dvaintridesetinka = 1/8
            //  Štiriinšestdesetinka = 1/16
            //  Stoosemindvajsetinka = 1/32 

            // Kaj se lahko pripeti:
            // Pika -> $duration * 2 / 3
            if(isset($note["dot"]) && $note["dot"]){
                $duration = $duration * 2 / 3;
            }

            // Triola -> $duration * num_notes / in_space_of
            if(isset($note["in_tuplet"])){
                $duration = $duration * $note["tuplet_type"]["num_notes"] / $note["tuplet_type"]["in_space_of"];
            }
            // To odpravi, pa bo :)


            $note["value"] = $duration * 4 / $divisions; 


        //}
        */
    }

    function getBarInfoFromMeasure($m){
        return array(
            "divisions" => (int) $m->attributes->divisions, // Divisions per quarter note
            // If divisions = 2
            "num_beats" => (int) $m->attributes->time->beats,
            "base_note" => (int) $m->attributes->time->{'beat-type'}
        );
    }

    function parseMusicXML($xml){
        $measures = $xml->xpath('//measure');
    
        $currentBarInfo = array();

        $takti = [];
        foreach($measures as $m){

            if(isset($m->attributes) && isset($m->attributes->time)){
                $currentBarInfo = getBarInfoFromMeasure($m);
            }
            // If $m includes attributes and attributes.divisions
            // Set the new barInfo

            $mN = [];

            $notes = $m->note;
            foreach($notes as $note){

                $jN = array();

                // Type
                if(isset($note->rest)) { $jN['type'] = "r"; } 
                else if(isset($note->pitch)) { $jN['type'] = "n"; }

                // Dot
                if(isset($note->dot)) { $jN['dot'] = true; }

                // Tie
                if(isset($note->tie) ) {
                    $kk = (string) $note->tie->attributes()->type;
                    if($kk == "stop")
                    {
                        $jN['tie'] = true;
                    }
                        
                }

                // Tuplet
                if(isset($note->{'time-modification'})){
                    $jN["in_tuplet"] = true;
                    
                    if(isset($note->notations->tuplet)){
                        $endTuplet = ((string) $note->notations->tuplet->attributes()->type) == "stop";
                        if($endTuplet){
                            $jN["tuplet_end"] = true;
                            $jN["tuplet_type"] = array(
                                "num_notes" => (int) $note->{'time-modification'}->{'actual-notes'},
                                "in_space_of" => (int) $note->{'time-modification'}->{'normal-notes'},
                            );
                        }
                    }
                }

                fillNoteValue($currentBarInfo, $jN, $note);

                // Note:
                //
                // {
                //     "type":"n", ✔
                //     "value":4,  ✔
                //     "dot":true, ✔
                //     "tie": true, ✔
                //     "in_tuplet":true, ✔
                //     "tuplet_end":true, ✔
                //     "tuplet_type": ✔
                //         {
                //             "num_notes":3,
                //             "in_space_of":2
                //         }
                // }
                //

        
                if(isset($jN["value"]))
                    $mN[] = $jN;
                
            }

            if(count($mN) > 0)
            {
                $takti[] = packMeasure($mN, $currentBarInfo);
            }
                

        }

        return $takti;
    }

    
    //$chosenFile = 'BrahWiMeSample.musicxml';
    $chosenFile = 'FaurReveSample.musicxml';
    $fileContents = file_get_contents('C:\\Users\\Lovro\\Downloads\\xmlsamples\\xmlsamples\\'.$chosenFile);

    $xml = new SimpleXMLElement($fileContents);
    foreach(parseMusicXML($xml) as $t){
        echo json_encode($t)."\r\n\r\n\r\n";
    }
    

?>