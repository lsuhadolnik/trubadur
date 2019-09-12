let Channel = require('./rhythmPlaybackChannel');
let util = require('./rhythmUtilities');
let Instruments = require('webaudio-instruments');

const RhythmPlaybackEngine = function() {

    if(!window.___player)
        window.___player = [];
    window.___player.push(this);

    this.metronome = true;

    this.lastNotes = [];
    this.BPM = 120;
    this.bar = {};

    this.playing = false;

    const player = new Instruments();

    const metronomeConfig = {
        program: 4,
        channel: 1,
        intensity: 3,
        constantDuration: 0.001
    };

    const trackConfig = {
        program: 18,
        channel: 0,
        intensity: 20,
    };

    let out = this;

    this.metronomeChannel = new Channel(player, metronomeConfig, () => {});
    this.trackChannel = new Channel(player, trackConfig, () => {out.playing = false;});

    this.percentPlayed = function(){ return currentNoteID / this.lastNotes.length; },

    this.playCountIn = function(bar, BPM, num_beats){

        let countinPitches   = util.get_countin_pitches(bar, num_beats);
        let countinNotes     = util.get_countin_notes  (bar, num_beats);
        let countInDurations = util.generate_playback_durations(countinNotes);

        let out = this;

        return new Promise(function(resolve, reject){
            out.metronomeChannel.play(BPM, bar, 
                countinPitches, countInDurations, function(){
                resolve();
            }, null);
        })

    };

    this.play = function(bar, BPM, values){

        this.stop();

        this.playing = true;

        if(!bar){
            bar = this.bar;
        }

        if(!BPM){
            BPM = this.BPM;
        }

        if(!values){
            values = this.lastNotes;
        }else {
            this.lastNotes = values;
        }

        let defaultPitch = [67];
        let notes = util.generate_playback_durations(values);

        var o2 = this;
        return this.playCountIn(bar, BPM, 1).then(() => {
            
            if(o2.metronome) {
                o2.playCountIn.apply(o2, [bar, BPM, 2]); // static num_bars...
            }
            
            return new Promise((resolve, reject) => {
                o2.trackChannel.play(BPM, bar, defaultPitch, notes, resolve);
            });
        });

    }

    this.stop = function() {
        this.metronomeChannel.stop();
        this.trackChannel.stop();
    }

    this.load = function(notes) {
        this.lastNotes = notes;
    }

    this.setBar = function(newBar){

        this.bar.num_beats = newBar.num_beats;
        this.bar.base_note = newBar.base_note;
        if(newBar.subdivisions){
            this.bar.subdivisions = newBar.subdivisions;
        }else {
            this.bar.subdivisions = null;
        }

    }

    this.setBPM = function(newBPM) {
        this.BPM = newBPM;
    }

};

export default RhythmPlaybackEngine;