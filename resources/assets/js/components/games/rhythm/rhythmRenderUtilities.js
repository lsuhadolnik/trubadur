var Fraction = require("fraction.js");

import Vex from 'vexflow'

let VF = Vex.Flow;

var RhythmRenderUtilities = function(){


    this._vex_draw_voice = function(context, stave, batchInfo, info, notes){

        let renderQueue = batchInfo.notes;
        let width = batchInfo.width;

        // Create a new voice everytime
        let voice = new VF.Voice(
            {
                num_beats: info.bar.num_beats,  
                beat_value: info.bar.base_note
            }
        );
        // DISABLE strict timing
        voice.setMode(Vex.Flow.Voice.Mode.SOFT);
        voice.setStrict(false);

        // Add render queue
        voice.addTickables(renderQueue);
        
        // var beams = VF.Beam.applyAndGetBeams(voice);
        var beams = VF.Beam.generateBeams(voice.getTickables(), {
            beam_rests: true,
            //beam_middle_only: true,
            show_stemlets: true,
            secondary_breaks: '8',
            groups: this._get_beam_grouping(info.bar)
        });

        let voiceOffset = 0;
        if(batchInfo.voiceOffset){
            voiceOffset = batchInfo.voiceOffset;
        }

        var formatter = new VF.Formatter();
        formatter.joinVoices([voice]);
        formatter.format([voice], width - voiceOffset);
        
        voice.draw(context, stave);
        
        this._vex_draw_optionals(context, beams);

    },

    this._get_beam_grouping = function(bar){

        if (parseInt(bar.num_beats) == 6 && parseInt(bar.base_note) == 8){
            return [new VF.Fraction(3, 8)]; // Naj se note grupirajo po četrtinki s piko
        }

        if(!bar.subdivisions){
            return [new VF.Fraction(1, 4)];
        } 
        else {
            return bar.subdivisions.map((s) => {
                return new VF.Fraction(s.n, s.d)
            });
        }

    }

    this._vex_draw_optionals = function(context, events){

        if(!events){
            return;
        }

        events.forEach(function(opt){
            opt.setContext(context).draw();
        });

    },

    this._vex_draw_staves = function(context, barInfo, info, timeSignatures){

        // info: {
        //     barOffsetY: int,
        //     bar: {
        //         num_beats: int,
        //         base_note: int
        //     }            
        // }
            
        var staveCount = barInfo.length;
        let staves = [];
        let startAtX = 0;

        let barlineX = [];

        for(let idx_bar = 0; idx_bar < barInfo.length; idx_bar++){

            let thisBar = barInfo[idx_bar];

            let thisWidth = thisBar.width;

            let stave = new VF.Stave(
                startAtX,   // X
                -info.barOffsetY,           // Y
                thisWidth              // Width
            );

            // Used for retrieving x-coords of barlines in function StaffView.retrieveXCoords
            barlineX.push(startAtX + thisWidth);

            startAtX += thisWidth;

            staves.push(stave);

            // If this is the first stave
            if(idx_bar == 0 && !info.hideTimeSignatures){
                // Add a clef and time signature.
                timeSignatures.forEach(b => {
                    stave.addTimeSignature(b)
                });
                
            }
            
            // Connect it to the rendering context
            stave.setContext(context);

        }

        let connectors = [];

        for(let idx_stave = 1; idx_stave < staveCount; idx_stave++){
            var connector = new VF.StaveConnector(staves[idx_stave - 1], staves[idx_stave]);
            
            if(info.hideTimeSignatures && idx_stave + 1 == staveCount)
                connector.setType(VF.StaveConnector.type.NONE);
            else if(idx_stave + 1 == staveCount)
                connector.setType(VF.StaveConnector.type.BOLD_DOUBLE_RIGHT);
            else 
                connector.setType(VF.StaveConnector.type.SINGLE);
            
            connector.setContext(context);
            connectors.push(connector);
        }

        // Draw the first stave
        staves[0].draw();

        for(let idx_stave = 1; idx_stave < staveCount; idx_stave++){
            staves[idx_stave].draw();
            connectors[idx_stave - 1].draw();
        }

        context.barlineX = barlineX;

        return staves;
    };

    this._construct_time_signature = function(bar){
        if(!bar.subdivisions){
            return [bar.num_beats + "/" + bar.base_note];
        } else {
            return bar.subdivisions.map(s => s.n + "/" + s.d);
        }
    }

    this._vex_render_batches = function(context, batches, optionals, info, notes, debug){


        // Redraw staves
        var barInfo = [];
        
        let timeSignatures = this._construct_time_signature(info.bar)

        batches.forEach((batchInfo, idx_bar) => {
            
            let width = batchInfo.width;

            // Make the first bar a bit wider for all time signatures to fit in.
            if(idx_bar == 0){
                width += 30 * (timeSignatures.length - 1) + 20;
            }

            barInfo.push({
                width: width // + parseInt(debug.offset2)// Fixed offset
            });    
        });

        barInfo.push({
            width: info.width // + parseInt(debug.offset1)
        });

        let staves = this._vex_draw_staves(context, barInfo, info, timeSignatures);

        for(var i = 0; i < batches.length; i++){
            this._vex_draw_voice(context, staves[i], batches[i], info, notes);
        }
        
        // Draw ties and tuplets
        optionals.forEach(opt => {
            this._vex_draw_optionals(context, opt);    
        });

    };

    this._min_dist = function(x_coords, x){

        let minIndex = 999;
        let minDiff  = 99999;
        let poss = x_coords;
        for(let i = 0; i < poss.length; i++){
            if(Math.abs(poss[i] - x) < minDiff){
                minDiff = Math.abs(poss[i] - x);
                minIndex = i;
            }
        }

        if(minDiff < 50){
            return {idx:minIndex, xpos: x_coords[minIndex], userx:x};
        }

        return {idx: -1, userx: x};

    };

    this._check_cursor_in_tuplet = function(cursor, notes){

        // Move this logic somewhere else
        // Nastavi lastnost cursor.in_tuplet
        // S tem skrijem gumbe takrat, ko sem v trioli, 
        /// zato da se ne dogajajo čudne stvari
        if(cursor.position - 1 >= 0 && notes.length > cursor.position - 1){
            let ccNote = notes[cursor.position - 1];
            if(ccNote.in_tuplet && !ccNote.hasOwnProperty("tuplet_end")){
                // Ni na zadnji noti triole
                cursor.in_tuplet = true;

            }
            else{
                // Je na zadnji noti triole
                cursor.in_tuplet = false;
            }
        }else{
            cursor.in_tuplet = false;
        }

    }

}

export default RhythmRenderUtilities;