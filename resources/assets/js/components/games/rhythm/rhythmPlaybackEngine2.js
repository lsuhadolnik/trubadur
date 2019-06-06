let engine = function(manager_ref, channel_info) {

    this.manager = manager_ref;
    this.channel = channel_info;
    this.midiChannel = 0;
    this.intensity = 127;

    this.playbackInfo = {
        noteId: 0,
        timeout: null
    }

    // Returns the percent of the playback
    this.percentPlayed = function(){

        return this.playbackInfo.noteId*100/(this.channel.durations.length);

    }

    // Checks if it can play next note, plays it and sets the next timeout
    this.play = function(endCallback, noteCallback){

        // Check if the note can play
        if(!this._checkIsPlaybackAllowedToContinue(endCallback)){ return; }

        // Get pitch and actual duration
        let actualDuration = this._getActualDuration();
        let pitch = this._getPitch();

        // Play the note
        this._playSingleNote(actualDuration, pitch);
        
        // Set everything needed for the playback to continue
        this._advancePlayback(noteCallback);

    }

    this._checkIsPlaybackAllowedToContinue = function(endCallback) {
        
        // Check if it is allowed to play
        if(!this.channel.status == "playing"){
            console.log("Engine is not allowed to play. Check manager status.");
            return false;
        }

        // This is the end - when the last note is played, 
        // the function gets called one more time, just for the callback to run
        if(this.playbackInfo.noteId >= this.channel.durations.length){

            // Call end callback and quit
            if(endCallback){ endCallback(this.channel);}
            return false;

        }

        // Ok, go on buddy
        return true;

    }

    this._advancePlayback = function(noteCallback) {

        // Advance one note        
        this.playbackInfo.noteId += 1;

        // Set the timeout for the next note to play
        let out = this;
        this.playbackInfo.timeout = setTimeout(function() {
            
            // Note has played out
            // Call the note callback
            if(noteCallback){ noteCallback(); }
            
            // Continue playing
            out.play(endCallback, noteCallback);

        }, actualDuration * 1000);

    }

    this._getPitch = function() {
        // pitch array length
        let lenMelody = this.channel.melody.length;
        // current playing note id
        let noteId = this.playbackInfo.noteId;
        // Get Pitch
        return this.channel.melody[Math.min(lenMelody - 1, noteId - 1)];
    }

    this._getActualDuration = function(){
        
        // current playing note id
        let noteId = this.playbackInfo.noteId;
        // This duration
        let dur = this.channel.durations[noteId];
        // Get Duration
        return actualDuration = Math.abs(dur.valueOf()) 
            / (this.manager.speed.BPM / 60) 
            * this.manager.bar_info.num_beats;

    }

    // plays one single note
    this._playSingleNote = function(actualDuration, sPitch){

        // Plays a note, stops automatically
        // Method returns immediately
        // MidiJS handles everything
        MIDI.noteOn(this.channel, sPitch, this.intensity, 0);
        MIDI.noteOff(this.channel, sPitch, actualDuration);

    }

};

module.exports = engine;