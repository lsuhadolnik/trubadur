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
        });


        var formatter = new VF.Formatter();
        formatter.joinVoices([voice]);
        formatter.format([voice], width);
        
        voice.draw(context, stave);
        
        this._vex_draw_optionals(context, beams);

        
    },

    this._vex_draw_optionals = function(context, events){

        if(!events){
            return;
        }

        events.forEach(function(opt){
            opt.setContext(context).draw();
        });

    },

    this._vex_draw_staves = function(context, barInfo, info){

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

        for(let idx_bar = 0; idx_bar < barInfo.length; idx_bar++){

            let thisBar = barInfo[idx_bar];

            let stave = new VF.Stave(
                startAtX,   // X
                -info.barOffsetY,           // Y
                thisBar.width              // Width
            );

            startAtX += thisBar.width;

            staves.push(stave);

            // If this is the first stave
            if(idx_bar == 0){
                // Add a clef and time signature.
                stave.addTimeSignature(
                    info.bar.num_beats
                    +"/"
                    +info.bar.base_note
                );
            }
            
            // Connect it to the rendering context
            stave.setContext(context);

        }

        let connectors = [];

        for(let idx_stave = 1; idx_stave < staveCount; idx_stave++){
            var connector = new VF.StaveConnector(staves[idx_stave - 1], staves[idx_stave]);
            
            if(idx_stave + 1 == staveCount)
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

        return staves;
    };

    this._vex_render_batches = function(context, batches, optionals, info, notes){

        // Redraw staves
        var barInfo = [];

        batches.forEach(batchInfo => {
            
            let width = batchInfo.width;

            barInfo.push({
                width: width + 60 // Fixed offset
            });    
        });

        barInfo.push({
            width: info.width
        });

        let staves = this._vex_draw_staves(context, barInfo, info);

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

    }

}

export default RhythmRenderUtilities;