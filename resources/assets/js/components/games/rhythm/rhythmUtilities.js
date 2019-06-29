//
// REFACTOR ASAP!!!! 
// 


var utilities = {

    generate_playback_durations: function(values, get_val){

        // Negativna trajanja pomenijo pavze
        // Kaj pa če so triole?
        // Potem trajanje vsake note ustrezno deli

        let nextHasTie = function(position){
            return values.length > position + 1 
            && values[position + 1].tie;
        }
        let nextIsRest = function(position){
            return values.length > position + 1 
            && values[position + 1].type == 'r';
        }
        let sumTiedDurations = function(cursorPosition, values){
            
            let duration = values[cursorPosition].duration;
            let numTies = 0;
            let pos = cursorPosition;
            
            while(nextHasTie(pos) && !nextIsRest(pos)){
                duration = duration.add(values[pos + 1].duration);
                numTies++; pos ++;
            }
            return {duration: duration, skips: numTies};

        }

        let getThisTupletType = function(values, idx){
            for(let i = idx; i < values.length; i++){
                let note = values[i];
                if(note.tuplet_end){
                    return note.tuplet_type;
                }
            }
            return -1;
        }

        let getDividedDurations = function(values){

            debugger;
            let current_tuplet_type = -1;
            let new_values = [];
            for(let i = 0; i < values.length; i++){
                if(values[i].in_tuplet){

                    if(current_tuplet_type == -1){
                        current_tuplet_type = getThisTupletType(values, i);
                        if(current_tuplet_type == -1){
                            alert("Nekaj je narobe, nisem našel zaključka triole. Ne morem ugotoviti trajanja.");
                            return null;
                        }
                    }

                    let nV = _.cloneDeep(values[i]);
                    nV.duration = nV.duration.div(current_tuplet_type);
                    if(nV.tuplet_end){
                        current_tuplet_type = -1;
                    }
                    new_values.push(nV);
                }
                else {
                    current_tuplet_type = -1;
                    new_values.push(values[i]);
                }
            }
            return new_values;

        }

        let realDurations = [];

        let notes = getDividedDurations(values);

        let skipN = 0;
        for(var noteIndex = 0; noteIndex < notes.length; noteIndex++){
            
            if(skipN > 0){ skipN --; continue; }

            let note = notes[noteIndex];

            if(note.type != "r")
            {
                let vals = sumTiedDurations(noteIndex, notes);   
                skipN = vals.skips;
                
                if(!get_val)
                    realDurations.push(vals.duration);
                else
                    realDurations.push(vals.duration.toFraction());

            }else {

                if(!get_val)
                    realDurations.push(note.duration.mul(-1));
                else
                    realDurations.push(note.duration.mul(-1).toFraction());
            }
        }

        console.log("REAL DURATIONS:");
        console.log(realDurations);

        return realDurations;

    },

    get_bar_count: function(notes){

        var count = 1;
        for(var i = 0; i < notes.length; i++){
            if(notes[i].type == "bar"){
                count++;
            }
        }

        return count;

    },

    generate_countin_durations: function(bar_info){



    },

    getNoteType: function(note){
        return parseInt(note.symbol);
    },

    sumTupletLength: function(notes, from, to) {
        let type = 0;
        let length = 0;
        for (let i = from; i < to && i < notes.length; i++) {
            const note = notes[i];
            if(type == 0){
                type = this.getNoteType(note);
                length++;
            }else{
                length += 1 / (this.getNoteType(note) / type)
            }
        }

        return parseInt(length);
    },

}

module.exports = utilities;
