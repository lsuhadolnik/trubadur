<?php

namespace App\Http\Controllers\Utils;

use App\RhythmBar;

class MusicXML
{

    public static function packMeasure(&$m, $barInfo) {
        unset($barInfo["divisions"]);
        return array(
            "content" => json_encode($m),
            "barInfo" => json_encode($barInfo),
            "difficulty" => 50
        );
    }

    public static function fillNoteValue($barInfo, &$note, $data){
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
            
        } 
    }

    public static function getBarInfoFromMeasure($m){
        return array(
            "divisions" => (int) $m->attributes->divisions, // Divisions per quarter note
            // If divisions = 2
            "num_beats" => (int) $m->attributes->time->beats,
            "base_note" => (int) $m->attributes->time->{'beat-type'}
        );
    }

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

    public static function parseMeasures($xml){
        $measures = $xml->xpath('//measure');

        $currentBarInfo = array();

        $takti = [];
        foreach($measures as $idxM => $m){

            if(isset($m->attributes) && isset($m->attributes->time)){
                $currentBarInfo = static::getBarInfoFromMeasure($m);
            }
            // If $m includes attributes and attributes.divisions
            // Set the new barInfo

            $mN = [];

            $notes = $m->note;
            $idxN = 0;
            foreach($notes as $note){
                $idxN++;
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

                static::fillNoteValue($currentBarInfo, $jN, $note);

                $staff = 1;
                $voice = 1;
                if(isset($note->staff)){
                    $staff = (int) $note->staff;
                }
                if(isset($note->voice)){
                    $voice = (int) $note->voice;
                }
        
                if(isset($jN["value"]) && !isset($note->chord)){

                    if(!isset($mN[$staff])){
                        $mN[$staff] = [];
                    }

                    if(!isset($mN[$staff][$voice])){
                        $mN[$staff][$voice] = [];
                    }

                    $mN[$staff][$voice][] = $jN;
                }
                    
                
            }

            foreach($mN as $staffs){
                foreach($staffs as $voice){
                    if(count($voice) > 0)
                    {
                        $takti[] = static::packMeasure($voice, $currentBarInfo);
                    }
                }
            }

        }

        return $takti;
    }

    public static function GetMeasureDatabaseIndex($takt){

        // find equal content
        $barsCollection = RhythmBar::whereRaw('content = CAST(? as JSON)', [$takt['content']])->get();
        if(count($barsCollection) == 0) {
            return null;
        }

        $bar = $barsCollection[0];
        
        // check equal barInfo
        $kkkk = print_r($bar, true);
        $a = json_decode($bar->barInfo);
        $b = json_decode($takt['barInfo']);
        if($a == $b){
            return $bar->id;
        } 

        return null;
        

    }

}