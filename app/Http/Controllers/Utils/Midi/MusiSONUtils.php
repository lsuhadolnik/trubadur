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

    /*



function _getNoteType(note) {
    return note.value;
}

function _get_countin_pitches(bar, num_bars) {

    if(!num_bars) num_bars = 1;

    // Original
    // [93, 86];

    const hi = 100;
    const lo = 76;

    let pitches = [];

    for(let ooo = 0; ooo < num_bars; ooo++){
        if(bar.subdivisions){

            bar.subdivisions.forEach(s => {
                pitches.push(hi);
                for (let i = 1; i < s.n; i++) { pitches.push(lo); }
            });

        } else if(!bar.subdivisions && bar.base_note == 8 && bar.num_beats == 6) {

            // Special counting for 6/8 time...
            pitches = pitches.concat([hi, lo, lo, hi, lo, lo]);

        } else {
            pitches.push(hi);
            for (let i = 1; i < bar.num_beats; i++) { pitches.push(lo); }
        }
    }

    return pitches;

}

function _get_countin_notes(bar, num_bars){

    if(!num_bars) num_bars = 1;

    var countInNotes = [];

    for(let vv = 0; vv < num_bars; vv++){
        if(bar.subdivisions){
            bar.subdivisions.forEach(sd => {
                for(let i = 0; i < sd.n; i++){
                    countInNotes.push({ type: 'n', value: sd.d });
                }
            });
        } else {
            for(let i = 0; i < bar.num_beats; i++){
                countInNotes.push({type: 'n', value: bar.base_note});
            }
        }
    }

    return countInNotes;
}

function _getBarLength(notes, stopOnBar){

    let length = 0;
    let tupletLength = 0;

    for(let i = 0; i < notes.length; i++){
        
        let note = notes[i];
        
        if(note.type == "bar"){
            if(stopOnBar){
                return length;
            }
            
            continue;
        }
        
        let dur = 4/note.value;
        if(note.dot){
            dur = dur*1.5;
        }

        if(note.in_tuplet){

            tupletLength += dur;

            if(note.tuplet_end){
                length += tupletLength/note.tuplet_type.num_notes*note.tuplet_type.in_space_of
                tupletLength = 0;
            }

        }else {
            length += dur;
        }
        
        
    }

    return length;
}

function _get_bar_length_properties(notes) {

    debugger;

    let length = _getBarLength(notes, false);
    let cross_bar = _getBarLength(notes, true);
    if(length > cross_bar){
        return { cross_bar, length };
    }

    return { length };

}

var utilities = {

    generate_playback_durations: _generate_playback_durations,
    getNotesDuration: _getBarLength,
    get_bar_length_properties: _get_bar_length_properties,
    getNoteType: _getNoteType,
    get_countin_notes: _get_countin_notes,
    get_countin_pitches: _get_countin_pitches,

    get_bar_count: function(notes){

        var count = 1;
        for(var i = 0; i < notes.length; i++){
            if(notes[i].type == "bar"){
                count++;
            }
        }

        return count;
    },

    sumTupletLength: function(notes, from, to) {
        let type = 0;
        let length = 0;
        for (let i = from; i < to && i < notes.length; i++) {
            const note = notes[i];
            if(type == 0){
                type = _getNoteType(note);
                length++;
            }else{
                length += 1 / (_getNoteType(note) / type)
            }
        }

        return parseInt(length);
    },

    check_notes_equal: function(exerciseNotes, userNotes){

        var soundsLikeFunc = _generate_playback_durations;

        // Return string fractions
        let ex = soundsLikeFunc(exerciseNotes);
        let us = soundsLikeFunc(userNotes);

        return _.isEqual(ex, us);
    },

}

module.exports = utilities;


    */

}