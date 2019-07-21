//
// REFACTOR ASAP!!!! 
// 
const Fraction = require('fraction.js');

function _step_durations_from_notes(notes){

    return notes.map(note => {
        if(note.type == "r")
            return new Fraction(-1, note.value);

        else if(note.type == "n")
            return new Fraction(1, note.value);

        else // bar
            return new Fraction(0);
    });

}

function _step_dots(notes, values){

    return values.map((v, i) => {

        if(notes[i].dot)
            return v.mul(1.5);
        
        return v;
    });

}

function _getThisTupletType(notes, idx){
    for(let i = idx; i < notes.length; i++){
        let note = notes[i];
        if(note.tuplet_end)
            return note.tuplet_type;
        
    }

    alert("Nekaj je narobe, nisem našel zaključka triole. Ne morem ugotoviti trajanja.")
    throw "[rhythmUtilities] tuplet_end missing.";
}

function _step_tuplets(notes, values) {


    let current_tuplet_type = null;
    return values.map((v,i) => {

        let nV = v;

        if(notes[i].in_tuplet){

            if(current_tuplet_type == null){
                current_tuplet_type = _getThisTupletType(notes, i);
            }

            nV = nV.div(current_tuplet_type.num_notes).mul(current_tuplet_type.in_space_of);

        }
        else {
            current_tuplet_type = null;
        }

        if(notes[i].tuplet_end){
            current_tuplet_type = null;
        }

        return nV;

    });

}

function _step_ties(notes, values) {

    let newValues = [];

    let currentDuration = new Fraction(0), 
        previousType = "bar";

    for(let i = 0; i < notes.length; i++){
        const note = notes[i], 
             value = values[i];

        if(note.type == "bar") continue;

        if(previousType == "bar"){
            previousType = note.type;
            currentDuration = currentDuration.add(value);
            continue;
        }

        if(note.type != previousType || !note.tie){
            newValues.push(currentDuration);
            currentDuration = new Fraction(0);
        }

        // Add to currentDuration
        currentDuration = currentDuration.add(value);
        
        previousType = note.type;

    }

    if(currentDuration.abs() != new Fraction(0)){
        newValues.push(currentDuration);
    }

    return newValues;

}

function _generate_playback_durations(notes){

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

    let v = [];
    v = _step_durations_from_notes(notes);
    v = _step_dots(notes, v);
    v = _step_tuplets(notes, v);
    v = _step_ties(notes, v); 
    // From here, notes and durations are not in sync anymore

    return v;

}

function _getNoteType(note) {
    return note.value;
}

var utilities = {

    generate_playback_durations: _generate_playback_durations,

    get_bar_count: function(notes){

        var count = 1;
        for(var i = 0; i < notes.length; i++){
            if(notes[i].type == "bar"){
                count++;
            }
        }

        return count;
    },

    getNoteType: _getNoteType,

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
    }

}

module.exports = utilities;
