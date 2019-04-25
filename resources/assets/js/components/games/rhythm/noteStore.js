var Fraction = require("fraction.js");

var NoteStore = function(bar, cursor, render_function, info) {

    // The supported note durations.
    // Currently supports up to a sixteenth note with a dot.
    this.supportedLengths = [1, 2, 4, 8, 16, 32];
    this.supportedRests   = [4, 8, 12, 16, 32];


    this.bar = bar;
    this.cursor = cursor;
    this.notes = [];

    this._call_render = function(){

        render_function(
            this.notes,
            this.cursor
        );

    }

    // Init notes with default
    // Initially the view is empty
    // This line is also responsible for rendering the empty bars on load
    this._call_render();

    this.handle_button = function(event) {

        if(event.type == 'n' || event.type == 'r' || event.type == 'bar')
        {   // This is a note

            /*if(this._sum_durations() > 2){
                alert("Trenutno je možno dodati največ dva takta");
                return;
            }*/

            this.add_note(event);
        } 
        else if(event.type == 'dot')
        {
            this.add_dot();
        }
        else if(event.type == 'tie')
        {
            this.add_tie();
        }
        else if(event.type == 'delete')
        {   // Delete (backspace)
            this.delete_note();
        }
        else if(event.type == '>')
        {
            this._move_cursor_forward();
            this._call_render();
        }
        else if(event.type == '<')
        {
            this._move_cursor_backwards();
            this._call_render();
        }
        else if(event.type == 'tuplet'){
            this.tuplet(event);
        }

    }

    this.clear = function(){
        
        this.notes = [];
        this.cursor.position = 0;
        
        this._call_render();

    }

    this.add_tie = function(){

        let n = this.cursor.position - 1;

        // Don't do anything if this is the first note...
        if(n <= 0) {
            return;
        }

        if(this.notes[n].type == "bar"){
            return;
        }

        this.notes[n].tie = !this.notes[n].tie;
        
        this._call_render();
        
    }

    this.add_dot = function() {

        let n = this.cursor.position - 1;

        // Don't do anything if this is the first note...
        if(n <= 0) {
            return;
        }

        if(this.notes[n].type == "bar"){
            return;
        }

        let note = this.notes[n];
        if(note.dot)
        {
            note.duration = note.duration.div(1.5);
            note.dot = false; 
        } 
        else 
        {
            note.duration = note.duration.mul(1.5);
            note.dot = true;    
        }
        
        this._call_render();

    }

    this.tuplet = function(event){

        let i = cursor.position - 1;

        if(!this.notes[i]){
            return;
        }

        if(this.notes[i].in_tuplet){
            this.remove_tuplet(event);
        }else{
            this.add_tuplet(event);
        }

    },

    this.remove_tuplet = function(event){

        // Remove tuplet in current position
        // Removes the tuplet even if the cursor is inside

        let i = cursor.position - 1;

        while(this.notes[i] && !this.notes[i].hasOwnProperty('tuplet_end')){
            i++;
        }
        if(!this.notes[i]){
            alert("Nekaj je narobe. Nisem našel zaključka triole... To je napaka v kodi.");
        }

        // i je na zaključku triole
        // Sprehodi se
        delete this.notes[i].tuplet_end;
        do {

            let note = this.notes[i];

            if(note.type == "bar"){
                return;
            }

            delete note.in_tuplet;
            note.duration = note.duration.mul(note.tuplet_type);

            delete note.tuplet_type;
            
            i--;
        } 
        // Odstranjuj, dokler traja ta triola ali ne trčiš ob drugo triolo
        while(i >= 0 && !this.notes[i].tuplet_end && this.notes[i].in_tuplet)


        // And render the result
        this._call_render()

    },

    this.clear_other_tuplet_snap_notes = function(pos){
        for(var i = 0; i < this.notes.length; i++){
            if(i != pos && this.notes[i].was_tuplet_end){
                delete this.notes[i].style;
                delete this.notes[i].was_tuplet_end;
            }
        }
    }

    this.check_if_there_were_any_tuplets_before = function(current_idx, max_dist){

        var potentialTupletEnd = -99999999;
        var potentialDist = Math.abs(potentialTupletEnd - current_idx) 

        for(var i = 0; i < this.notes.length; i++){

            var thisDist = Math.abs(i - current_idx)

            if(this.notes[i].was_tuplet_end && thisDist <= max_dist && thisDist < potentialDist){
                potentialTupletEnd = i;
            }
        }

        if(potentialTupletEnd >= 0){
            return potentialTupletEnd;
        }

        return -1;

    }

    this.add_tuplet = function(event) {

        // Original cursor position
        var n = cursor.position - 1;
        if(n < 0 || this.notes.length < n){
            return;
        }
        
        var lastNote = this.notes[n];
        if(lastNote.type == "bar" || lastNote.in_tuplet){
            return;
        }

        // Check if behind a valid note
        if(["2","2r","4", "4r", "8", "8r", "16", "16r"].indexOf(lastNote.symbol) < 0){
            return; 
        }

        // Delete this note
        lastNote = _.clone(lastNote);
        this.notes.splice(n, 1);

        // New symbol
        let newSymbol = (lastNote.duration.d * 2) + (lastNote.type == "r" ? "r" : "");


        // Add three half-shorter-lasting notes
        let k = [];
        for(let i = 0; i < event.tuplet_type; i++){
            let newNote = {
                type: lastNote.type,
                symbol: newSymbol,
                duration: lastNote.duration.div(event.tuplet_type),
                in_tuplet: true,
                tuplet_type: event.tuplet_type,
                overwrite: true,
            };
            if(i+1 == event.tuplet_type){
                newNote.tuplet_end = true;
            }
            k.push(newNote);
        }
        this.notes.splice(n, 0, ...k);
        this.cursor.position = n;

        this._call_render();

    }

    /*this.add_tuplet = function(event){

        // Original cursor position
        var thePosition = cursor.position;

        // Stick to notes that were already tuplets
        var newPosition = this.check_if_there_were_any_tuplets_before(cursor.position, 2);
        if(newPosition >= 0){
            this.clear_other_tuplet_snap_notes(newPosition);
            thePosition = newPosition + 1;
            delete this.notes[newPosition].style;
            delete this.notes[newPosition].was_tuplet_end;
        }

        
        // Add tuplet to notes behind the cursor

        if(this.notes[thePosition - 1].in_tuplet){
            alert("Ne morem dodati triole v triolo");
            return;
        }

        // Check if there are enough notes
        // - Check if the duration sums up to (1/baseNote)
        // - Notes must fit exactly (watch out for the dots)
        let currentDuration = new Fraction(0);
        let toIndex = -1;
        for(let i = thePosition - 1; i >= 0; i--){
            currentDuration = currentDuration.add(this.notes[i].duration);
            let value = currentDuration.compare(new Fraction(event.tuplet_type, bar.base_note))
            if(value == 0){
                // Enough notes for a tuplet
                toIndex = i;
                break;
            }
            else if(value > 0){
                // Tuplet would not fit
                alert("Tu ne morem narediti triole, ker se notne vrednosti ne seštejejo ustrezno.");
                return;
            }
        }
        if(toIndex < 0){
            // Not enough notes for a tuplet
            alert("Tu ne morem narediti triole, ker ni dovolj not");
            return;
        }

        // For those notes in the tuplet
        // - Change duration to (duration / tuplet_type)
        // - Add tuplet_from, tuplet_to
        for(let i = toIndex; i < thePosition; i++){
            this.notes[i].duration = this.notes[i].duration.div(event.tuplet_type);
            this.notes[i].in_tuplet = true;
            this.notes[i].tuplet_type = event.tuplet_type;
        }
        this.notes[thePosition - 1].tuplet_from = toIndex;
        this.notes[thePosition - 1].tuplet_to = thePosition;

        // And render the result
        this._call_render()

    }*/


    this._is_supported_length = function(event){

        if(event.tuplet_type == 3){
            return true;
        }

        // Check if the note is in supported range...
        for(let i = 0; i < this.supportedLengths.length; i++)
            if (event.duration.d == this.supportedLengths[i])
                return true

        console.error("Note length not supported... ("+event.duration.d+")");
        return false;
        
    },


    this.add_note = function(event) {
        
        let i = this.cursor.position;
        if(i >= 0 && i < this.notes.length && this.notes[i].overwrite && event.type != "bar"){
            this.overwrite_next(event);
            return;
        }

        this.remove_all_overwrites();

        // Check if in tuplet
        if(i >= 0 && i < this.notes.length 
            && this.notes[i].in_tuplet
            && this.notes[i - 1].in_tuplet
            && !this.notes[i - 1].tuplet_end){
            alert("CANNOT! "+i);
            return;
        }

        if(event.type != "bar" && !this._is_supported_length(event)){
            return;
        }

        // Add the note
        // Add the new note to the current position (at the cursor)
        this.notes.splice(this.cursor.position, 0, event);
        
        // Move cursor forward
        this._move_cursor_forward();

        // And render the result
        this._call_render()
    },

    this.overwrite_next = function(event){

        //debugger;
        let i = this.cursor.position;
        let overwriteNote = this.notes[i];
        let oldDur = parseInt(overwriteNote.symbol);

        // I can only fit an equal or smaller event here
        let newDur = parseInt(event.symbol);
        if(oldDur > newDur){
            return;
            // Cannot fit bigger events here.
        }

        
        //

        if(oldDur == newDur){
            // Prepiši prvo noto v vseh primerih
            delete overwriteNote.overwrite;
            overwriteNote.symbol = event.symbol;
            overwriteNote.type = event.type;
            this.cursor.position = i + 1;
            
            // Je že vse narjeno

        }else {

            // Poglej, kolikokrat je manjša enota
            /*let times = Math.floor(newDur / oldDur) - 1;

            // Dodaj toliko - 1 pavzo
            for(let a = 0; a < times; a++){
                let copy = _.clone(overwriteNote);
                copy.symbol = parseInt(overwriteNote.symbol) + "r";
                copy.type = "r";
                copy.overwrite = true;

                // Tole je slabo. Izboljšaj
                if(overwriteNote.tuplet_end && a+1 == times){

                }else{
                    delete copy.tuplet_end;
                }
                

                this.notes.splice(i + 1 + a, 0, copy);
            }
            
            // Pavze so kopije osnovnih objektov
            // Odstrani tuplet_from in tuplet_to iz pavz
            */
            alert("To pa še ne deluje.");


        }

        
        this._call_render();

    },

    this._sum_durations = function(){

        let sum = new Fraction();
        for(var i = 0; i < this.notes.length; i++){
            sum = sum.add(this.notes[i].duration);
        }
        return sum;

    },

    this.delete_note = function() {
        
        // If I'm are at the beginning, I can't delete anything else
        if(this.cursor.position == 0)
        {
            // So I quit
            return;
        }

        if(this.notes.length > this.cursor.position && this.notes[this.cursor.position].tie){
            delete this.notes[this.cursor.position].tie;
        }

        if(this.notes.length > this.cursor.position + 1 && this.notes[this.cursor.position + 1].tie){
            delete this.notes[this.cursor.position + 1].tie;
        }

        if(this.notes[this.cursor.position - 1].in_tuplet){
            this.remove_tuplet();
        }

        // Move one note back - so the situation is as follows
        //
        //         |  (note to delete) (rest/note) ... (rest/note)
        // cursor--^
        this._move_cursor_backwards();
        
        // Delete this note
        this.notes.splice(this.cursor.position, 1);

        this.remove_all_overwrites();
        
        // And render the result
        this._call_render()

    }

    this.remove_all_overwrites = function() {
        for(let i = 0; i < this.notes.length; i++){
            if(this.notes[i].overwrite){
                delete this.notes[i].overwrite;
            }
        }
    }

    this._move_cursor_forward = function(){

        if(this.cursor.position < this.notes.length){
            this.cursor.position ++;
        }
        else{
            this.cursor.position = this.notes.length;
        }

    }

    this._move_cursor_backwards = function(){

        if(0 >= this.cursor.position)
            this.cursor.position = 0;
        else
            this.cursor.position --;

    }


    this.check_sum_fit = function() {


        let currentDuration = new Fraction(0);
        for(let i = 0; i < this.notes.length; i++){
            
            currentDuration = currentDuration.add(this.notes[i].duration);
            let value = currentDuration.compare(1);
            if(value == 0){
                currentDuration = new Fraction(0);
                continue;
            }
            else if(value > 0){
                return false;
            }

        }

        return true;

    }



    
}

export default NoteStore