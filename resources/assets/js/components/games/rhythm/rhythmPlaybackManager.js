let RhythmUtilities = require("./rhythmUtilities");


// Manager allows easy use of the rhythm playback engine

// The manager should have references to all available playback engines
// References should be organized into a flat structure

// What it does
// - it provides channels: for exercise, for user input, for countin...
// - it provides playlists: if you want to play countin before the sequence
//      or chain multiple playbacks
let m = {

    // Has to be an object, to be able to send it around as a reference
    // speed: {
    //     BPM: 120
    // },
    speed: null,

    // List of channels to play one after another
    playlist: [],

    // Has to be an object, to be able to send it around as a reference
    // Has roots in Rhythm.vue
    //
    // bar_info: {
    //     num_beats: 4,
    //     base_note: 4
    // },
    bar_info: null,


    // channel structure:
    // - name (name of the channel key)
    // - melody: array of MIDI pitches; holds on the last one if there are too many notes
    // - status: 
    //      - playing (is playing currently), 
    //      - paused (is ready to play, not playing currently), 
    //      - stopped (not ready to play, must me reloaded)
    // - durations: the generated rhythm durations
    channels: {

        // Example:
        //
        // exercise: {
        //     name: "exercise",
        //     melody: [],
        //     status: 'stopped',
        //     engine: ... the rhythm engine,
        //     durations: [] rhythm durations
        // }

    },

    _playbackFinishedCallback(channel) {
            
        // Stop when the playback finishes
        this.stop(channel);

        if(this.playlist.length > 0){

        }

    },

    // Get channel by name or channel itself
    _getChannel: function(x) {
        if(typeof x === "object"){
            return x;
        }

        if(typeof x === "string"){

            if(!this._checkChannelExists(x)){ return null; }

            return this.channels[x];
        }
    },

    // Inits a new channel
    //
    newChannel: function(channelDef) {},

    // - play: resumes a channel from pause; 
    //      when it reaches the end, it stops (resets playback to beginning) 
    //      and does not unload the sequence
    //
    play: function(channel_) {

        let ch = this._getChannel(channel_);
        if(ch == null || !this._checkChannelCanPlay(ch)) { 
            
            return; 

        }

        // Start playing
        ch.status = 'playing';
        ch.engine.play(this._playbackFinishedCallback, null);
        
    },

    _checkChannelCanPlay: function(channel_) {
        
        let channel = this._getChannel(channel_);
        if(channel == null) { return false; }

        if(channel.status == "playing"){

            console.error("The channel "+channel.name+" is already playing.");
            return false;
        }

        if(this.channels[channelName].status != "paused"){
            
            console.error("The channel "+channel.name+" cannot play, because it is not paused. You must load it first.");            
            return false;

        }

        return true;

    },

    _checkChannelIsPlaying: function(channel_) {

        let channel = this._getChannel(channel_);
        if(channel == null) { return false; }

        if(channel.status != "playing"){
            console.error("Channel "+channel.name+" is not playing.");
            return false;
        }

    },

    _checkChannelExists: function(channelName) {

        if(this.channels[channelName] == null){

            console.error("Channel " + channelName + " does not exist.");
            return false;

        }

        return true;
    },

    // - pause: pauses a channel
    //
    pause: function(channel_) {

        let channel = this._getChannel(channel_);
        if(channel == null) { return false; }

        if(channel == null || !this._checkChannelIsPlaying(channel)){ return; }

        channel.status = "paused";

    },

    // - stop: resets the , can start playing from beginning
    //
    stop: function(channel_) {

        let channel = this._getChannel(channel_);
        if(channel == null) { return false; }

        channel.status = "paused";
        channel.engine.stop();

    },

    // - toggle: if paused, then play; if playing then pause
    //
    toggle: function(channel_) {

        let channel = this._getChannel(channel_);
        if(channel == null) { return false; }

        if(channel.status == "playing"){
            this.pause(channelName);
        }

        if(channel.status == "paused"){
            this.play(channelName);
        }

    },
    
    // - loadNotes: receives note objects, converts them to durations and calls this.load()
    //
    loadDurations: function(channel_, durations) {

        let channel = this._getChannel(channel_);
        if(channel == null) { return false; }

        if(channel.status == "playing"){
            channel.status = "stopped";
            channel.engine.stop();
        }

        channel.durations = durations;
        channel.status = "paused";
    },

    // - load: receives durations and prepares the channel to play
    loadNotes: function(channel_, notes) {

        this.loadDurations(
            channel_, 
            RhythmUtilities.generate_playback_durations(notes)
        );
    }

};


// It is a module
//
module.exports = m;