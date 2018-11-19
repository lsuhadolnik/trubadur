<template>

    <div class="rhythm-game__staff">

        <div class="rhythm-game__staff__first-row"  id="first-stave"></div>
        <div class="rhythm-game__staff__second-row" id="second-stave"></div>

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
            },

            VF: {
                renderer: null,
                context: null,
                first_stave:  {
                    stave: null,
                    voice: null
                },
            }
        }
    },
    methods: {

        _clear: function(){
            this.VF.context.clear();
        },

        _vex_draw_voice: function(renderQueue, optionals){

            // Create new voice everytime

            // Create a voice in 4/4 and add above notes
            this.VF.first_stave.voice = new VF.Voice(
                {
                    num_beats: this.bar.num_beats,  
                    beat_value: this.bar.base_note
                }
            );
            // DISABLE strict timing
            this.VF.first_stave.voice.setMode(Vex.Flow.Voice.Mode.SOFT);
            this.VF.first_stave.voice.setStrict(false);

            this.VF.first_stave.voice.addTickables(renderQueue);

            //var beams = VF.Beam.generateBeams(this.VF.first_stave.voice);
            //VF.Formatter.FormatAndDraw(
            //    this.VF.context, 
            //    this.VF.first_stave.stave, 
            //    this.VF.first_stave.voice
            //);
            

            // Been trying different factors.
            // If you change this, make sure, that 16 sixteenth notes fit onto the screen.
            var maxNotesWidth = this.info.width * 0.8;

            var formatter = new VF.Formatter()
                .format([this.VF.first_stave.voice], this.info.width * 0.8);

            // Render voice
            //beams.forEach(function(b) {b.setContext(context).draw()})

            this.VF.first_stave.voice.draw(this.VF.context, this.VF.first_stave.stave);
            
            

            // Draw optionals...
            if(optionals) {

                if(optionals.ties) {

                    let data = this;
                    // Draw ties
                    optionals.ties.forEach(function(t) {t.setContext(data.VF.context).draw()})

                }

            }

        },

        _vex_draw_stave: function(){
            
            // Create a stave at position 0, 0 of width 400 on the canvas.
            this.VF.first_stave.stave = new VF.Stave(0,0, this.info.width);

            // Add a clef and time signature.
            this.VF.first_stave.stave.addTimeSignature(
                this.bar.num_beats
                +"/"
                +this.bar.base_note
            );

            // Connect it to the rendering context and draw!
            this.VF.first_stave.stave.setContext(this.VF.context).draw();
        },

        render(notes, cursor) {

            // notes = [
            //      {type: "n", symbol: "4",  duration: new Fraction(3).div(8), dot: true},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(8)},
            //      {type: "n", symbol: "8",  duration: new Fraction(3).div(8), dot: true},
            //      {type: "n", symbol: "16", duration: new Fraction(1).div(16)},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3, tie:true},
            //      {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3, tie:true},
            //  ];

            // Clear all notes from svg
            this._clear();

            // Redraw staves
            this._vex_draw_stave();

            let ties = [];

            var renderQueue = [];
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
            }

            // Render the Voice
            this._vex_draw_voice(renderQueue, {
                ties: ties
            });

        }
    },
    mounted(){

        // INIT

        // VexFlow Magic
        let VF = Vex.Flow;

        // Create an SVG renderer and attach it to the DIV element named "boo".
        var div = document.getElementById("first-stave")
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
