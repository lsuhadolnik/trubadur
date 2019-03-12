var Fraction = require("fraction.js");

var RhythmPlaybackEngine = function(){

    this.generate_playback_durations = function(values, get_val){

        // Negativna trajanja pomenijo pavze

        let nextHasTie = function(position){
            return values.length > position + 1 
            && values[position + 1].tie;
        }
        let nextIsRest = function(position){
            return values.length > position + 1 
            && values[position + 1].type == 'r';
        }
        let sumTiedDurations = function(cursorPosition){
            
            let duration = values[cursorPosition].duration;
            let numTies = 0;
            let pos = cursorPosition;
            
            while(nextHasTie(pos) && !nextIsRest(pos)){
                duration = duration.add(values[pos + 1].duration);
                numTies++; pos ++;
            }
            return {duration: duration, skips: numTies};

        }

        let realDurations = [];

        let skipN = 0;
        for(var noteIndex = 0; noteIndex < values.length; noteIndex++){
            
            if(skipN > 0){ skipN --; continue; }

            let note = values[noteIndex];

            if(note.type != "r")
            {
                let vals = sumTiedDurations(noteIndex);   
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

        // if(get_val){
        //     console.log(realDurations);
        // }else{
        //     console.log(this.get_duration_values(realDurations));
        // }

        return realDurations;

    };

    this.intensity = 127;
    this.pitch = 60;


    this.playing = false;
    this.playbackQueue = [];
    this.loaded = false;
    this.currentNoteID = null;
    this.currentTimeout = null;
    this.currentPlaybackTime = 0;
    
    
    // This is an object, so the changes of property values can be tracked.
    this.throttleInfo = {
        throttle: 2
    };

    this.playNote = function(){

        let sustain = 1/32;

        if(!this.playing 
            || this.playbackQueue == null 
            || this.playbackQueue.length == 0
            || this.loaded == false
            || this.currentNoteID == null
            || this.currentNoteID >= this.playbackQueue.length){
            
                this.playing = false;
                this.currentNoteID = 0;
                return;
        }
            
        this.playing = true;
        let dur = this.playbackQueue[this.currentNoteID++];
        let actualDuration = 0;

        if(dur > 0)
        {

            actualDuration = dur.valueOf();

            MIDI.noteOn(0, this.pitch, this.intensity, 0);
            MIDI.noteOff(0, this.pitch, actualDuration);

        }else {
            actualDuration = -dur.valueOf();
        }

        let milliseconds = actualDuration * 1000;
        console.log("Will last for "+milliseconds);

        let outside = this;
        if(this.playing) {
            this.currentTimeout = setTimeout(function() {
                console.log("I'm playing right now and you cant stop me.");
                outside.playNote();
            }, milliseconds);
        }
            
    }

    this.play = function(){

        if(!this.loaded){
            alert("No notes to play. Before the playback, you must call the load() method.");
            return;
        }

        if(this.playing){
            return;
        }

        this.playing = true;
        this.playNote();

        /**
         * Note so naložene v polju playbackQueue
         * Predvajaj noto na položaju currentNoteID - kliči noteOn za to noto in povečaj currentNoteID za 1
         * Nastavi timeout, ki naj se sproži po trajanju note. 
         *      noteOff() - takoj
         *      Naj kliče isto funkcijo za predvajanje note
         * 
         */

    }

    this.load = function(values){

        if(this.playing){
            this.playing = false;
            clearTimeout(this.currentTimeout);
        }

        this.playing = false;
        this.currentNoteID = 0;
        this.currentTimeout = null;
        this.currentNoteID = 0;
        this.currentPlaybackTime = 0;

        this.playbackQueue = this.generate_playback_durations(values);
        this.loaded = true;
    }

    this.pause = function(){

        clearTimeout(this.currentTimeout);
        this.playing = false;

    }

    this.stop = function() {

        this.playing = false;
        this.loaded = false;
        this.playbackQueue = [];

    }

    this.playback = function(values, throttle) {
        
        var currentTime = 0;
        let durations = this.generate_playback_durations(values);

        let intensity = 127;

        for(var idx_duration = 0; idx_duration < durations.length; idx_duration++){

            let dur = durations[idx_duration];

            if(dur > 0)
            {
                MIDI.noteOn(0, 60, intensity, currentTime);
                
                currentTime += dur.valueOf() * throttle;
                MIDI.noteOff(0, 60, currentTime);

            }else {
                currentTime += -dur.valueOf() * throttle;
            }
        }

    }

}

export default RhythmPlaybackEngine;