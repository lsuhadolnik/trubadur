
module.exports = function(player, conf, playingStopped){

    let defaultConfig = {
        program: 4,
        channel: 1,
        intensity: 10,
        constantDuration: null
    }

    let currentNoteID = 0;

    // Setup default config
    conf = {...defaultConfig, ...conf};

    let playing = false;
    let currentTimeout = null;

    let playerContext = this;

    let _playNote = function(BPM, bar, pitch, notes, endCallback, noteCallback){

        if(!playing || notes == null || notes.length <= currentNoteID)
        {
            if(endCallback){
                endCallback();
            }

            this.stop();
            return;
        }
        
        // Current duration
        let dur = notes[currentNoteID];

        // BPM logic
        // Brez spreminjanja trajanja velja, da je celinka dolga 1s
        // torej je vsaka četrtinka dolga 0,25s, kar je 4 BPS, kar je 240 BPM
        // realDuration = koliko sekund naj traja nota...
        let realDuration = dur.valueOf() * bar.base_note * (60 / BPM);

        if(dur > 0)
        {

            // Tole sem naredil samo zato, da prvo noto pri count-inu drugače zapoje
            // Lahko bi kdaj v prihodnosti dodali melodično-ritmični narek...
            // S tem Math.min sem hotel tudi povedati, da naj se ustavi na zadnjem pitchu, ki je podan.
            let sPitch = pitch[Math.min(pitch.length - 1, currentNoteID)];

            let engineDuration = (realDuration - 0.15);
            if(conf.constantDuration != null) {
                engineDuration = conf.constantDuration;
            }

            player.play (
                conf.program,        // instrument: 24 is "Acoustic Guitar (nylon)"
                sPitch,        // note: midi number or frequency in Hz (if > 127)
                1,       // velocity: 0..1
                0,         // delay in seconds
                engineDuration, // Duration in seconds
                conf.channel         // Channel
            )

        }else {
            // Rest or unknown
            realDuration = -realDuration;
        }

        let milliseconds = realDuration * 1000;

        currentTimeout = setTimeout(() => {
            
            if(noteCallback){
                noteCallback();
            }
            
            _playNote.apply(playerContext, [BPM, bar, pitch, notes, endCallback, noteCallback]);
        }, milliseconds);

        currentNoteID++;
            
    };



    this.play = (BPM, bar, pitch, notes, endCallback, noteCallback) => {
        playing = true;
        currentNoteID = 0;

        _playNote(BPM, bar, pitch, notes, endCallback, noteCallback);
    };

    this.stop = function() {
        
        if(playingStopped){
            playingStopped();
        }
            
        playing = false;

        if(currentTimeout){
            clearTimeout(currentTimeout);
        }
        currentTimeout = null;
    }

}