<template>

    <div class="rhythm-game__staff">

        <div class="rhythm-game__staff__first-row"  id="first-row"></div>
        <div class="rhythm-game__staff__second-row" id="second-row"></div>

    </div>

</template>

<script>

import Vex from 'vexflow'

var Fraction = require('fraction.js');

let VF = Vex.Flow;
let StaveNote = VF.StaveNote;
let Tuplet = VF.Tuplet;

export default {

    props: [
        'bar', 'cursor'
    ],

    data () {
        return {

            info: {
                width: window.innerWidth * 0.98,
                height: 200,
                staveCount: 2
            },

            VF: {
                renderer: null,
                context: null,
                staves:  [],
            }
        }
    },
    methods: {

        _clear: function(){
            this.VF.context.clear();
        },

        _vex_draw_voice: function(stave, renderQueue, optionals){

            // Create a new voice everytime

            let voice = new VF.Voice(
                {
                    num_beats: this.bar.num_beats,  
                    beat_value: this.bar.base_note
                }
            );
            // DISABLE strict timing
            voice.setMode(Vex.Flow.Voice.Mode.SOFT);
            voice.setStrict(false);

            // Add render queue
            voice.addTickables(renderQueue);
            

            // Been trying different factors.
            // If you change this, make sure, that 16 sixteenth notes fit onto the screen.
            var maxNotesWidth = this.info.width * 0.8;

            var formatter = new VF.Formatter()
                .format([voice], maxNotesWidth);

            // Render voice
            //beams.forEach(function(b) {b.setContext(context).draw()})

            voice.draw(this.VF.context, stave);
            
            // Draw optionals...
            if(optionals) {

                if(optionals.ties) {

                    let data = this;
                    // Draw ties
                    optionals.ties.forEach(function(t) {t.setContext(data.VF.context).draw()})

                }

            }

        },

        _vex_draw_staves: function(){
            
            this.VF.staves = [];

            for(let idx_stave = 0; idx_stave < this.info.staveCount; idx_stave++){

                let staveHeight = 60;

                let stave = new VF.Stave(
                    0,
                    staveHeight * idx_stave, 
                    this.info.width);


                this.VF.staves.push(stave);

                // If this is the first stave
                if(idx_stave == 0){
                    // Add a clef and time signature.
                    stave.addTimeSignature(
                        this.bar.num_beats
                        +"/"
                        +this.bar.base_note
                    );
                }
                

                // Connect it to the rendering context and draw!
                stave.setContext(this.VF.context).draw();

            }

        },

        render(notes, cursor) {
            // Render onto n bars. Assumes no bar overlapping...

            // notes = [
            //      {type: "n", symbol: "4",  duration: new Fraction(3).div(8), dot: true},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(8), tie: true}, // Last 2 notes are tied
            //      {type: "n", symbol: "8",  duration: new Fraction(3).div(8), dot: true},
            //      {type: "n", symbol: "16", duration: new Fraction(1).div(16)},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3, tie:true},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3, tie:true},
            //  ];

            // Clear all notes from svg
            this._clear();

            // Redraw staves
            this._vex_draw_staves();

            let staveIndex = 0;

            var ties = [];
            var renderQueue = [];
            var currentDuration = new Fraction(0);
            for(let i = 0; i < notes.length; i++){

                // Bye bye, false note
                if(!notes[i]) { continue; }
                    
                // Handle notes and rests
                let newNote = new StaveNote(
                    {
                        clef: "treble", 
                        keys: ["g/4"], 
                        duration: notes[i].symbol 
                    }
                );

    	        // Handle dots
                if(notes[i].dot)
                    newNote.addDot(0); // enako je tudi newNote.addDotToAll()

                if(notes[i].tie && i > 0){
                
                    // tie is:
                    //  - this note + last note
                    ties.push(new VF.StaveTie({
                        first_note: renderQueue[i - 1],
                        last_note:  newNote,
                        first_indices: [0],
                        last_indices:  [0]
                    }));

                }

                if(i == cursor.position){
                    newNote.setStyle({
                        fillStyle: "blue", 
                        strokeStyle: "blue"
                    });
                }

                renderQueue.push(newNote);
                currentDuration = currentDuration.add(notes[i].duration);

                
                if(currentDuration.compare(new Fraction(1)) == 0){
                    
                    this._vex_draw_voice(this.VF.staves[staveIndex], renderQueue, {
                        ties: ties
                    });

                    renderQueue = [];
                    ties = [];
                    staveIndex ++;
                    currentDuration = new Fraction(0);
                }

            }

            if(renderQueue.length > 0){
                // Draw the rest
                this._vex_draw_voice(this.VF.staves[staveIndex + 1], renderQueue, {
                    ties: ties
                });
            }
            

        }
    },
    mounted(){

        // INIT

        // VexFlow Magic
        let VF = Vex.Flow;

        // Create an SVG renderer and attach it to the DIV element named "boo".
        var div = document.getElementById("first-row")
        this.VF.renderer = new VF.Renderer(div, VF.Renderer.Backends.SVG);

        // Size our svg:
        this.VF.renderer.resize(
            this.info.width,
            this.info.height,
        );

        // And get a drawing context:
        this.VF.context = this.VF.renderer.getContext();
        
    },

}
</script>
