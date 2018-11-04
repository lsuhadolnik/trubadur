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
                width: 300,
                height: 100,
            },

            VF: {
                renderer: null,
                context: null,
                first_stave:  {
                    stave: null,
                    voice: null
                },
                second_stave: {
                    stave: null,
                    voice: null
                }
            }
        }
    },
    methods: {
        test_render: function(event){

            
            var notes = [
                // A quarter-note C.
                new StaveNote({clef: "treble", keys: ["g/4"], duration: event.symbol }),
            ];

            this.VF.first_stave.voice.addTickables(notes);

            var formatter = new VF.Formatter()
                .format([this.VF.first_stave.voice], this.info.width);

            // Render voice
            this.VF.first_stave.voice.draw(this.VF.context, this.VF.first_stave.stave);

        },

        render(notes) {

            // notes = [
            //     {type: "n", symbol: "4",  duration: new Fraction(3).div(8), dot: true},
            //     {type: "n", symbol: "8",  duration: new Fraction(1).div(8)},
            //     {type: "n", symbol: "8",  duration: new Fraction(3).div(8), dot: true},
            //     {type: "n", symbol: "16", duration: new Fraction(1).div(16)},
            //     {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3},
            //     {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3},
            //     {type: "n", symbol: "8",  duration: new Fraction(1).div(12), tuple_type: 3},
            // ];

            var renderQueue = [];

            var tuple_buffer = [];
            var last_tuple_type = -1;

            for(let i = 0; i < notes.length; i++){

                let newNote = new StaveNote(
                    {
                        clef: "treble", 
                        keys: ["g/4"], 
                        duration: notes[i].symbol 
                    }
                );

                if(notes[i].dot)
                    newNote.addDot(0); // enako je tudi newNote.addDotToAll()

                // HANDLE TUPLETS
                // TODO: Handle different tuple length
                //if(notes[i].tuple_type ){

                //    tuple_buffer.push(newNote);
                //    last_tuple_type = notes[i].tuple_type;

                //     TODO!! HANDLE tuplet type change... če je bla prej triola in potem ni več
                //} else {

                //    if(tuple_buffer){
                //        renderQueue.push(new Tuplet(tuple_buffer));
                //        tuple_buffer = [];
                //        last_tuple_type = -1;
                //    }

                //    
                //}

                renderQueue.push(newNote);

            }

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

            
            var formatter = new VF.Formatter()
                .format([this.VF.first_stave.voice], this.info.width);

            // Render voice
            this.VF.first_stave.voice.draw(this.VF.context, this.VF.first_stave.stave);

            let dataVF = this.VF;
            var beams = VF.Beam.generateBeams(renderQueue);
            beams.forEach(function(b) {b.setContext(dataVF.context).draw()});


        }
    },
    mounted(){

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

        // Create a stave at position 10, 40 of width 400 on the canvas.
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

}
</script>
