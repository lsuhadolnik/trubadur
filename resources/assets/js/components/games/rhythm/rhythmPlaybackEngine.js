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

    this.BPM = 60;

    this.channel = 0;

    var outside = this;

    this.intensity = 127;
    this.pitch = [60];

    this.currentlyLoaded = "";
    this.percentPlayed = 0;

    this.playing = false;
    this.playbackQueue = [];
    this.loaded = false;
    this.currentNoteID = null;
    this.currentTimeout = null;
    this.currentPlaybackTime = 0;

    this.bar_info = null;

    this.countIn = false;

    this.countInPlayback = null;
    
    // This is an object, so the changes of property values can be tracked.
    this.throttleInfo = {
        throttle: 2
    };

    this.playNote = function(endCallback, noteCallback){

        let sustain = 1/32;

        if(!this.playing 
            || this.playbackQueue == null 
            || this.playbackQueue.length == 0
            || this.loaded == false
            || this.currentNoteID == null
            || this.currentNoteID >= this.playbackQueue.length){
            
                if(endCallback){
                    endCallback();
                }
                    
                this.playing = false;
                this.currentNoteID = 0;
                return;
        }
            
        this.playing = true;
        let dur = this.playbackQueue[this.currentNoteID++];
        let actualDuration = 0;

        if(dur > 0)
        {

            // BPM logic
            // Brez spreminjanja trajanja velja, da je celinka dolga 1s
            // torej je vsaka četrtinka dolga 0,25s, kar je 4 BPS, kar je 240 BPM

            actualDuration = dur.valueOf() / (this.BPM / 60) * this.bar_info.num_beats;

            // WTF?! Hahaha :D
            // Tole sem naredil samo zato, da prvo noto pri count-inu drugače zapoje
            // Za ostale primere je približno neuporabno (no, lahko bi kdaj v prihodnosti dodal melodično-ritmični narek...)
            // S tem sem hotel povedati, da naj se ustavi na zadnjem pitchu, ki je podan.
            let sPitch = this.pitch[Math.min(this.pitch.length - 1, this.currentNoteID - 1)];

            // Zaigraj, ustavi se samodejno.
            MIDI.noteOn(this.channel, sPitch, this.intensity, 0);
            MIDI.noteOff(this.channel, sPitch, actualDuration);

        }else {
            actualDuration = -dur.valueOf();
        }

        let milliseconds = actualDuration * 1000;
        console.log("Will last for "+milliseconds);

        let outside = this;
        if(this.playing) {
            this.currentTimeout = setTimeout(function() {
                
                if(noteCallback){
                    noteCallback();
                }
                
                outside.playNote(endCallback, noteCallback);
            }, milliseconds);
        }
            
    }

    this.playCountIn = function(then){

        if(!this.countInPlayback){
            this.countInPlayback = new RhythmPlaybackEngine();
            this.countInPlayback.bar_info = this.bar_info;
            this.countInPlayback.channel = 1;
            this.countInPlayback.pitch = [93, 86];
            this.countInPlayback.load(this.getCountInNotes());
        }

        this.countInPlayback.BPM = this.BPM;

        this.countInPlayback.resume(function(){
            //Done
            if(then){
                then();
            }
        });

    }

    this.play = function(){

        var o2 = this;
        this.playCountIn(function() {
            o2.resume();
        });

    }

    this.saveState = function(){

        var m = {
            currentlyLoaded: _.clone(outside.currentlyLoaded),
            percentPlayed: _.clone(outside.percentPlayed),
            playing: _.clone(outside.playing),
            playbackQueue: _.cloneDeep(outside.playbackQueue),
            loaded: _.clone(outside.loaded),
            currentNoteID: _.clone(outside.currentNoteID),
            currentTimeout: _.clone(outside.currentTimeout),
            currentPlaybackTime: _.clone(outside.currentPlaybackTime),
            countIn: _.clone(outside.countIn)
        };
        return m;

    }

    this.restoreState = function(m){

        outside.currentlyLoaded = m.currentlyLoaded;
        outside.percentPlayed = m.percentPlayed;
        outside.playing = m.playing;
        outside.playbackQueue = m.playbackQueue;
        outside.loaded = m.loaded;
        outside.currentNoteID = m.currentNoteID;
        outside.currentTimeout = m.currentTimeout;
        outside.currentPlaybackTime = m.currentPlaybackTime;
        outside.countIn = m.countIn;

    }

    this.resume = function(endCallback, noteCallback){

        if(!this.loaded){
            alert("No notes to play. Before the playback, you must call the load() method.");
            return;
        }

        if(this.playing){
            return;
        }


        this.playing = true;
        this.playNote(endCallback, noteCallback);

        /**
         * Note so naložene v polju playbackQueue
         * Predvajaj noto na položaju currentNoteID - kliči noteOn za to noto in povečaj currentNoteID za 1
         * Nastavi timeout, ki naj se sproži po trajanju note. 
         *      noteOff() - takoj
         *      Naj kliče isto funkcijo za predvajanje note
         * 
         */

    }

    this.load = function(values, playbackName){

        if(this.playing){
            this.playing = false;
            clearTimeout(this.currentTimeout);
        }

        this.playing = false;
        this.currentTimeout = null;
        this.currentNoteID = 0;
        this.currentPlaybackTime = 0;

        if(playbackName){
            if(playbackName == "countin"){
                this.countIn = true
            }else{
                this.currentlyLoaded = playbackName;
            }
            
        }

        this.playbackQueue = this.generate_playback_durations(values);
        this.loaded = true;
    }

    this.getCountInNotes = function(){

        var countInNotes = [];
        for(var i = 0; i < this.bar_info.num_beats; i++){
            countInNotes.push({
                type: 'n',
                duration: new Fraction(1, this.bar_info.base_note)
            });
        }

        return countInNotes;
    }

    this.pause = function(){

        //this.currentNoteID += 1;
        clearTimeout(this.currentTimeout);
        this.playing = false;
        
    }

    this.stop = function() {

        this.playing = false;
        this.loaded = false;
        this.playbackQueue = [];

    }

}

export default RhythmPlaybackEngine;