var Fraction = require("fraction.js");

var NoteStore = function(bar, cursor, render_function, info) {

    // The supported note durations.
    // Currently supports up to a sixteenth note with a dot.
    this.supportedLengths = [1, 2, 4, 8, 16, 32];
    this.supportedRests   = [4, 8, 12, 16, 32];

    //alert(staff_view.info.staveCount);

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
    this._call_render();

    this.handle_button = function(event) {

        if(event.type == 'n' || event.type == 'r')
        {   // This is a note
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

    this.add_tie = function(){
        
        let n = this.cursor.position - 1;

        // Don't do anything if this is the first note...
        if(n <= 0) {
            return;
        }

        this.notes[n].tie = !this.notes[n].tie;
        
        this._call_render();
        
    }

    this.add_dot = function() {

        this._move_cursor_backwards()
        let note = _.clone(this.notes[this.cursor.position]);

        this._move_cursor_forward()
        this.delete_note();

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
        
        this.add_note(note);

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
        let tuplet_type = this.notes[i].tuplet_type

        while(this.notes[i] && !this.notes[i].hasOwnProperty('tuplet_from')){
            i++;
        }
        if(!this.notes[i]){
            alert("Nekaj je narobe. Nisem našel zaključka triole... To je napaka v kodi.");
        }

        this.notes.slice(this.notes[i].tuplet_from, this.notes[i].tuplet_to).forEach(note => {
            delete note.in_tuplet;
            delete note.tuplet_type;
            note.duration = note.duration.mul(tuplet_type);
        });

        delete this.notes[i].tuplet_from;
        delete this.notes[i].tuplet_to;

        // And render the result
        this._call_render()

    },

    this.add_tuplet = function(event){

        // Add tuplet to notes behind the cursor

        if(this.notes[cursor.position - 1].in_tuplet){
            alert("Ne morem dodati triole v triolo");
            return;
        }

        // Check if there are enough notes
        // - Check if the duration sums up to (1/baseNote)
        // - Notes must fit exactly (watch out for the dots)
        let currentDuration = new Fraction(0);
        let toIndex = -1;
        for(let i = cursor.position - 1; i >= 0; i--){
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
        for(let i = toIndex; i < cursor.position; i++){
            this.notes[i].duration = this.notes[i].duration.div(event.tuplet_type);
            this.notes[i].in_tuplet = true;
            this.notes[i].tuplet_type = event.tuplet_type;
        }
        this.notes[cursor.position - 1].tuplet_from = toIndex;
        this.notes[cursor.position - 1].tuplet_to = cursor.position;

        // And render the result
        this._call_render()

    }

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

    this._fix_tuplet_indices_forward = function(num){
        for(let i = this.cursor.position; i < this.notes.length; i++){
            if(this.notes[i].hasOwnProperty('tuplet_from')){
                this.notes[i].tuplet_from += num;
                this.notes[i].tuplet_to += num;
            }
        }
    },

    this.add_note = function(event) {

        let MAX_DURATION = info.staveCount;
        
        if(!this._is_supported_length(event)){
            return;
        }

        /*if(this._sum_durations().add(event.duration) > MAX_DURATION){
            alert("Nota je predolga");
            return;
        }*/

        // Add the note
        // Add the new note to the current position (at the cursor)
        this.notes.splice(this.cursor.position, 0, event);
        
        /*if(!this.check_sum_fit()){
            alert("Takt je predolg");

            this.notes.splice(this.cursor.position - 1, 1);
            return;
        }*/
        
        // Move cursor forward
        this._move_cursor_forward();

        // Popravi tuplet_from, tuplet_to na koncih triol
        // Povečaj za ena. Vedno se dodaja samo en element ob enkrat
        this._fix_tuplet_indices_forward(+1);

        // And render the result
        this._call_render()
    },

    this._sum_durations = function(){

        let sum = new Fraction();
        for(var i = 0; i < this.notes.length; i++){
            sum = sum.add(this.notes[i].duration);
        }
        return sum;

    },

    this.delete_note = function() {
        
        // If you are at the beginning, you can't delete anything else
        if(this.cursor.position == 0)
        {
            // So you quit
            return;
        }

        if(this.notes.length > this.cursor.position && this.notes[this.cursor.position].tie){
            delete this.notes[this.cursor.position].tie;
        }

        if(this.notes[this.cursor.position - 1].in_tuplet){
            this.remove_tuplet();
        }

        // Move one note back - so the situation is as follows
        //
        //         |  (note to delete) (rest/note) ... (rest/note)
        // cursor--^
        this._move_cursor_backwards();

        this._fix_tuplet_indices_forward(-1);
        
        // Delete this note
        this.notes.splice(this.cursor.position, 1);

        
        // And render the result
        this._call_render()

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