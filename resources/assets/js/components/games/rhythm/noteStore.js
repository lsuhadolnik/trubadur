var Fraction = require("fraction.js");


var NoteStore = function(bar, cursor, render_function) {

    this.bar = bar;
    this.cursor = cursor;
    this.notes = [ // TODO!!!
        {type:"r", symbol: "4r", duration: new Fraction(1,4)},
        {type:"r", symbol: "4r", duration: new Fraction(1,4)},
        {type:"r", symbol: "4r", duration: new Fraction(1,4)},
        {type:"r", symbol: "4r", duration: new Fraction(1,4)}
    ];

    // Init notes with default
    render_function(this.notes);

    this.handle_button = function(event) {

        if(event.type == 'n'){ // This is a note
            this.add_note(event);
        }

    }

    this.add_note = function(event) {

        // Poštimej triole... Ta postopek odpade, če so notri triole
        // Mogoče dej triole kar direkt v pod-array...
        var rests_info = this.sum_silence_until_edited();

        if(rests_info.duration < event.duration){
            alert("Nota je predolga.");
            return;
        }

        let remaining = rests_info.duration.sub(event.duration); // prostor - trajanje
        
        // Delete rests
        this.notes.splice(rests_info.rests[0], rests_info.rests.length);
        let ostanek = this.notes.splice(this.cursor.position);

        // Add note
        this.notes.splice(this.cursor.position, 0, event);
        
        new_rests = this.generate_rests_for_duration(remaining);
        this.notes = notes.concat(new_rests).concat(ostanek);


        render_function(this.notes)
    }

    this.sum_silence_until_edited = function(){

        let duration = new Fraction(0);
        let rests = [];
        let i = this.cursor.position;
        while(i < this.notes.length){
            if(this.notes[i].user_edited || this.notes[i].type != "r"){
                break;
            }
            duration = duration.add(this.notes[i].duration);
            rests.push(i);
            i++;
        }

        return {
            duration, rests
        };
    }

    this.generate_rests_for_duration = function(remaining) {
        debugger;
        let rests = [];
        while(remaining > 0){

            if(remaining.compare(new Fraction(1,4)) >= 0){
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
            }

        }
    }

}

export default NoteStore