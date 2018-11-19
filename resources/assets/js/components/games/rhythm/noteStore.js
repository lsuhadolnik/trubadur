var Fraction = require("fraction.js");



var NoteStore = function(bar, cursor, render_function) {

    // The supported note durations.
    // Currently supports up to a sixteenth note with a dot.
    this.supportedLengths = [1, 2, 4, 8, 16, 32];
    this.supportedRests   = [4, 8, 16, 32];

    this.bar = bar;
    this.cursor = cursor;
    this.notes = [ // TODO!!!
        {type:"r", symbol:"wr", duration:new Fraction(1)}
    ];

    this._call_render = function(){

        render_function(
            this.notes,
            this.cursor
        );

    }

    // Init notes with default
    this._call_render();

    this.handle_button = function(event) {

        if(event.type == 'n')
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
    }

    this.add_tie = function(){
        
        let n = this.cursor.position;

        // Don't do anything if this is the first note...
        if(n <= 0) {
            return;
        }

        this.notes[n].tie = !this.notes[n].tie;
        
        this._call_render();
        
    }

    this.add_dot = function() {

        this._move_cursor_backwards()
        let note = this.notes[this.cursor.position];

        this._move_cursor_forward()
        this.delete_note();
        
        if(note.dot){
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

    this.add_note = function(event) {

        // Check if the note is in supported range...
        let supported = false;
        for(let i = 0; i < this.supportedLengths.length && !supported; i++)
            if (event.duration.d == this.supportedLengths[i])
                supported = true; 
        
        if(!supported)
        {
            console.error("Note length not supported... ("+event.duration.d+")");
            return;
        }
           
        //console.log("Checking for tie..")
        if(this.cursor.position > 0 
            && this.cursor.position < this.notes.length
            && this.notes[this.cursor.position].tie){
                event.tie = true;
        }
            

        var rests_info = this.sum_silence_until_edited();
        // RETURNS: object
        //      rests_info.rests -> array    -    indices of summed rests
        //      rests_info.duration -> Fraction - duration of summed rests

        // If the event duration exceeds the duration of summed notes
        if(rests_info.duration < event.duration){
            // Notify user
            alert("Nota je predolga.");
            // and Quit
            return;
        }
        // Get the remaining silence duration - will be filled with new rests
        let remaining = rests_info.duration.sub(event.duration); // prostor - trajanje
        
        // --- Delete rests ---
        // What this splice does:  splice(from_idx, num_elements_to_delete, [el1, el2, ...])
        // *** IT ALTERS THE ORIGINAL ARRAY (this.notes) ***
        //      -   from the position of the first rest
        //      -   delete all rests (number to delete = length of all rests)
        //      -   discard the remainder (deleted rests)
        this.notes.splice(rests_info.rests[0], rests_info.rests.length);
        // What this splice does:
        // *** IT ALTERS THE ORIGINAl ARRAY (this.notes) ***
        //      -   from current position (at the cursor)
        //      -   delete all notes
        //      -   store the deleted notes in ostanek
        //  All non-rests are collected here.
        //  example: 
        //  
        //         | rest  rest  rest  note note note ...
        // cursor--^   ---deleted---    ----ostanek-----
        let ostanek = this.notes.splice(this.cursor.position);

        // Add the note
        // Add the new note to the current position (at the cursor)
        this.notes.splice(this.cursor.position, 0, event);
        
        // Now generate new rests to fill the remaining space
        // Returns an array of event objects - rests
        let new_rests = this.generate_rests_for_duration(remaining);
        // Add all up: 
        // 
        //  | (new note) (new rest) ... (new rest) (ostanek_note) ... (ostanek_note)
        // 
        this.notes = this.notes.concat(new_rests).concat(ostanek);
        // Move cursor forward
        this._move_cursor_forward();

        // And render the result
        this._call_render()
    }

    this.delete_note = function() {
        
        // If you are at the beginning, you can't delete anything else
        if(this.cursor.position == 0)
        {
            // So you quit
            return;
        }

        // Move one note back - so the situation is as follows
        //
        //         |  (note to delete) (rest/note) ... (rest/note)
        // cursor--^
        this._move_cursor_backwards();
        // Change current note to a rest
        this.notes[this.cursor.position].type = "r";

        // Sum up all rests from current position
        // If there are any more rests, they will get added too
        //
        //      | (rest) ... (rest) (note)
        //
        var rests_info = this.sum_silence_until_edited();
        // RETURNS: object
        //      rests_info.rests -> array    -    indices of summed rests
        //      rests_info.duration -> Fraction - duration of summed rests
        let remaining = rests_info.duration;
        
        // Delete rests
        this.notes.splice(rests_info.rests[0], rests_info.rests.length);
        let ostanek = this.notes.splice(this.cursor.position);
        
        // Now generate new rests to fill the remaining space
        // Returns an array of event objects - rests
        let new_rests = this.generate_rests_for_duration(remaining);
        // Add all up: 
        // 
        //  | (new note) (new rest) ... (new rest) (ostanek_note) ... (ostanek_note)
        // 
        this.notes = this.notes.concat(new_rests).concat(ostanek);

        
        // And render the result
        this._call_render()

    }

    this._move_cursor_forward = function(){

        if(this.notes.length <= this.cursor.position)
            this.cursor.position = this.notes.length - 1;
        else
            this.cursor.position ++;

    }

    this._move_cursor_backwards = function(){

        if(0 >= this.cursor.position)
            this.cursor.position = 0;
        else
            this.cursor.position --;

    }

    this.sum_silence_until_edited = function(){

        // Instantiate the duration and rests array
        let duration = new Fraction(0);
        let rests = [];

        // Start at the cursor position
        let i = this.cursor.position;
        // Visit all following notes
        while(i < this.notes.length){
            // First, check the condition
            // Until a note or a user-edited rest is met...
            if(this.notes[i].user_edited || this.notes[i].type != "r")
            {
                // Then exit the loop
                break;
            }

            duration = duration.add(this.notes[i].duration);
            // Add the index of the rest
            rests.push(i);
            // Move forward
            i++;
        }

        return {
            duration, rests
        };
    }

    this.generate_rests_for_duration = function(remaining) {
        
        // OMG!
        // Please don't look at it.
        // I will improve it once, I promise.

        let rests = [];
        // While there is some space left to fill
        while(remaining > 0){

            // Try different durations...
            // Try the longer durations first, and add them to back...
            // This will keep smaller duarions together
            //  Example:
            //      (16) (16r) (8r) (4r) (4r) (4r)
            //
            let durations = this.supportedRests;
            for(var idx_duration = 0; idx_duration < durations.length; idx_duration++){

                let duration = durations[idx_duration];

                // If it fits...
                if(remaining.compare(new Fraction(1,duration)) >= 0){
                    // Divide the remaining duration with duration...
                    // This yields the number of notes of this duration that can be filled
                    // Get the whole part
                    var num = remaining.mul(duration).floor();
                    // Keep subtracting for as long as the note fits...
                    for(var i = 0; i < num; i++){
                        rests.unshift({
                            type:"r", 
                            symbol: duration.toString()+"r", 
                            duration: new Fraction(1,duration)
                        });
                        remaining = remaining.add(new Fraction(-1, duration));
                    }

                    // Else if... 
                    break; // Break the first loop
                }    
            }

            /*if(remaining.compare(new Fraction(1,4)) >= 0){
                var num = remaining.mul(4).floor();
                for(var i = 0; i < num; i++){
                    rests.unshift({type:"r", symbol: "4", duration: new Fraction(1,4)});
                    remaining = remaining.add(new Fraction(-1,4));
                }
            } else if(remaining.compare(new Fraction(1,8)) >= 0){
                var num = remaining.mul(8).floor();
                for(var i = 0; i < num; i++){
                    rests.unshift({type:"r", symbol: "8", duration: new Fraction(1,8)});
                    remaining = remaining.add(new Fraction(-1,8));
                }
            } else if(remaining.compare(new Fraction(1,16)) >= 0){
                var num = remaining.mul(16).floor();
                for(var i = 0; i < num; i++){
                    rests.unshift({type:"r", symbol: "16", duration: new Fraction(1,16)});
                    remaining = remaining.add(new Fraction(-1,16));
                }
            } */
        }
        return rests;
    }


    
}

export default NoteStore