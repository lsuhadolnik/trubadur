<?php

namespace App\Http\Controllers\Utils\Midi;

use Phospr\Fraction;

class MusiSONUtils {

    private static function _step_durations_from_notes($notes){

        return array_map(function($note) {
            if($note->type == "r")
                return new Fraction(-1, $note->value);
    
            else if($note->type == "n")
                return new Fraction(1, $note->value);
    
            else // bar
                return new Fraction(0);
        }, $notes);
    
    }

    public static function _step_dots($notes, $values){

        return array_map(function($v, $i) use($notes) {
    
            if(isset($notes[$i]->dot) && $notes[$i]->dot)
                return $v->multiply(new Fraction(3, 2));
            
            return $v;
        }, $values, array_keys($values));

    }

    public static function _getThisTupletType($notes, $idx){
        for($i = $idx; $i < count($notes); $i++){
            $note = $notes[$i];
            if(isset($note->tuplet_end) && $note->tuplet_end)
                return $note->tuplet_type;
            
        }

        throw new \Exception("[rhythmUtilities] tuplet_end missing.");
    }
    
    public static function _step_tuplets($notes, $values) {
    
    
        $current_tuplet_type = null;
        return array_map(function($v, $i) use($notes, $current_tuplet_type) {
    
            $nV = $v;
    
            if(isset($notes[$i]->in_tuplet) && $notes[$i]->in_tuplet){
    
                if($current_tuplet_type == null){
                    $current_tuplet_type = static::_getThisTupletType($notes, $i);
                }
    
                $nV = $nV->multiply(new Fraction(
                    $current_tuplet_type->in_space_of,
                    $current_tuplet_type->num_notes
                    )
                );
    
            }
            else {
                $current_tuplet_type = null;
            }
    
            if(isset($notes[$i]->tuplet_end) && $notes[$i]->tuplet_end){
                $current_tuplet_type = null;
            }
    
            return $nV;
    
        }, $values, array_keys($values));
    
    }

    public static function _step_ties($notes, $values) {

        $newValues = [];
    
        $currentDuration = new Fraction(0); 
        $previousType = "bar";
    
        for($i = 0; $i < count($notes); $i++){
            $note  = $notes[$i];
            $value = $values[$i];
    
            if($note->type == "bar") continue;
    
            if($previousType == "bar"){
                $previousType = $note->type;
                $currentDuration = $currentDuration->add($value);
                continue;
            }
    
            if($note->type != $previousType || !(isset($note->tie) && $note->tie)){
                $newValues[] = $currentDuration;
                $currentDuration = new Fraction(0);
            }
    
            // Add to currentDuration
            $currentDuration = $currentDuration->add($value);
            
            $previousType = $note->type;
    
        }
    
        if(abs($currentDuration->getNumerator()) > 0){
            $newValues[] = $currentDuration;
        }
    
        return $newValues;
    
    }

    public static function _step_pauses($values) {

        $newValues = [];
    
        $currentDuration = null;
    
        for($i = 0; $i < count($values); $i++){
            $value = $values[$i];
    
            if($value->getNumerator() < 0) {
    
                if($currentDuration != null){
                    $currentDuration = $currentDuration->add($value);
                }else {
                    $currentDuration = $value;
                }
    
            } else {
    
                if($currentDuration != null){
                    $newValues[] = $currentDuration;
                    $currentDuration = null;
                }
    
                $newValues[] = $value;
    
            }
    
        }
    
        if($currentDuration != null){
            $newValues[] = $currentDuration;
        }
    
        return $newValues;
    
    }

    public static function toDurations($notes){

        // Negativna trajanja pomenijo pavze
        // Kaj pa če so triole?
        // Potem trajanje vsake note ustrezno deli
    
        // FILTER PATTERN
        // at the end, the function must return durations in fractions, 
        // so there is no need for keeping the notes in sync
        // 
        // _step_durations_from_notes ->
        // _step_dots ->
        // _step_tuplets ->
        // _step_ties
    
        $v = [];
        $v = static::_step_durations_from_notes($notes);
        $v = static::_step_dots($notes, $v);
        $v = static::_step_tuplets($notes, $v);
        $v = static::_step_ties($notes, $v); 
        // From here, notes and durations are not in sync anymore
        $v = static::_step_pauses($v); 
        
    
        return $v;
    
    }

}