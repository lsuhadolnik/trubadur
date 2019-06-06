var Fraction = require("fraction.js");
let RhythmUtilities = require('./rhythmUtilities');

var RhythmPlaybackEngine = function(){

    this.BPM = 120;

    this.channel = 0;
    this.intensity = 127;
    this.pitch = [65];

    this.currentlyLoaded = "";
    this.playing = false;
    this.playbackQueue = [];
    this.loaded = false;
    this.currentNoteID = null;
    this.currentTimeout = null;

    this.bar_info = null;

    this.countInPlayback = null;

    this.percentPlayed = function(){
        return this.currentNoteID*100/(this.playbackQueue.length);
    }

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
            
            // Copied code from up
            actualDuration = -dur.valueOf() / (this.BPM / 60) * this.bar_info.num_beats;
        }

        let milliseconds = actualDuration * 1000;
        //console.log("Will last for "+milliseconds);

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

    this.resume = function(endCallback, noteCallback){

        if(!this.loaded){
            console.log("No notes to play. Before the playback, you must call the load() method.");
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

        if(playbackName){
            if(playbackName == "countin"){
                this.countIn = true
            }else{
                this.currentlyLoaded = playbackName;
            }
            
        }

        this.playbackQueue = RhythmUtilities.generate_playback_durations(values);
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

        this.currentlyLoaded = "";
        this.playing = false;
        this.playbackQueue = [];
        this.loaded = false;
        this.currentNoteID = null;

        if(this.currentTimeout){
            clearTimeout(this.currentTimeout);
        }
        this.currentTimeout = null;
        
    }

}

export default RhythmPlaybackEngine;