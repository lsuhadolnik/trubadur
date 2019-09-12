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

function _step_pauses(values) {

    let newValues = [];

    let currentDuration = null;

    for(let i = 0; i < values.length; i++){
        const value = values[i];

        if(value.valueOf() < 0) {

            if(currentDuration != null){
                currentDuration = currentDuration.add(value);
            }else {
                currentDuration = value;
            }

        } else {

            if(currentDuration != null){
                newValues.push(currentDuration);
                currentDuration = null;
            }

            newValues.push(value);

        }

    }

    if(currentDuration != null){
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
    v = _step_pauses(v); 
    

    return v;

}

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

function _getBarLength(notes){

    let length = 0;
    let tupletLength = 0;
    for(let i = 0; i < notes.length; i++){
        let note = notes[i];
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

var utilities = {

    generate_playback_durations: _generate_playback_durations,
    getNotesDuration: _getBarLength,
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
