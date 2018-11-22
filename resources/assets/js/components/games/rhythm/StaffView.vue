<template>

    <div class="rhythm-game__staff">
        
        <div class="rhythm-game__staff__first-row" @click="_minimap_clicked">
            <div id="first-row" ></div>
        </div>
        
        <div class="rhythm-game__staff__second-row">
            <div id="second-row"></div>
        </div>
        
    </div>

</template>

<style lang="scss" scoped>

    .rhythm-game__staff__first-row {
        
    }

    #first-row {
        transform: scale(0.5) translate(-50%, 0);
    }

    .rhythm-game__staff__second-row {
        /*background:red;*/
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
        height: 145px;
        /*scroll-behavior: smooth;
        -webkit-scroll-behavior: smooth;*/
    }

    #second-row {
        -webkit-transform: scale(2) translate(25%, 25%);
        transform: scale(2) translate(25%, 25%);
    }

</style>


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
                width: 2*(window.innerWidth),
                height: 60,
                staveCount: 2,
                barWidth: window.innerWidth,
                barHeight: 60,
                barOffsetY: 30,

                maxStaveWidth: 320,

                lastMinimapBubbleX: 0,
                lastMinimapBubbleW: 0
            },

            VF: {
                el_ids : [
                    {
                        id: "first-row",
                        role: "minimap",
                        bubble_class: "minimap-bubble"
                    }, 
                    {
                        id: "second-row",
                        role: "zoomview"
                    }
                ]
            }
        }
    },
    methods: {

        _vex_draw_voice: function(context, stave, renderQueue, optionals){

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
            var maxNotesWidth = this.info.width * 0.8 * 0.5;

            var formatter = new VF.Formatter()
                .format([voice], maxNotesWidth);

            // Render voice
            //beams.forEach(function(b) {b.setContext(context).draw()})

            voice.draw(context, stave);
            
            // Draw optionals...
            if(optionals) {

                if(optionals.ties) {

                    optionals.ties.forEach(function(t) {t.setContext(context).draw()})

                }

            }
        },

        _vex_draw_staves: function(context){
            
            let staves = [];

            for(let idx_stave = 0; idx_stave < this.info.staveCount; idx_stave++){

                let stave = new VF.Stave(
                    this.info.barWidth * idx_stave,
                    -this.info.barOffsetY, // staveHeight * idx_stave
                    this.info.width);

                staves.push(stave);

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
                stave.setContext(context).draw();

            }

            return staves;

        },

        _minimap_clicked(event) {

            let v = document.getElementById("second-row").parentNode;

            // Content width
            let contentWidth = v.scrollWidth;

            // Screen width
            let screenWidth = window.innerWidth;

            // Touch X
            let touchX = event.clientX;

            let sDoSomeMath = (touchX / screenWidth) * contentWidth - screenWidth / 2;

            v.scroll(sDoSomeMath, 0);

            // Animate bubble
            let x =  (touchX / screenWidth) * minimapWidth;
            x = x - screenWidth*3/8;
            x = Math.min(Math.max(0, x), minimapWidth - this.info.lastMinimapBubbleW);
            let bubble_class = this.VF.el_ids[0].bubble_class;
            let rect = document.querySelector("."+bubble_class);
            rect.setAttribute("x", x);

        },

        _cursor_rendered(descriptor, cursorNode){

            if(!cursorNode)
                return;

            let zoomViewScroll = document.getElementById("second-row").parentNode;

            let scrollWidth = zoomViewScroll.scrollWidth;            
            let screenWidth = window.innerWidth;
        
            let bbox = cursorNode.attrs.el.getBoundingClientRect();
            let startX = bbox.left + bbox.width / 2;

            zoomViewScroll.scroll({
                left: startX - screenWidth*3/4,
                top: 0,
                behavior: 'smooth'
            });
            
            if(descriptor.role == "minimap") {

                // GET THAT RECT
                let rect = document.querySelector("."+descriptor.bubble_class);
                
                let svg = descriptor.context;
                let minimapWidth = svg.width;

                let width = (screenWidth / scrollWidth) * minimapWidth;

                let x =  (startX / screenWidth) * minimapWidth;
                x = x - screenWidth*3/8;
                x = Math.min(Math.max(0, x), minimapWidth - width);
                
                this.info.lastMinimapBubbleX = x;
                this.info.lastMinimapBubbleW = width;

                rect.setAttribute("x", x);
                rect.setAttribute("width", width);


            }            

        },

        _render_context(descriptor, notes, cursor){

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

            // element from el_ids array
            // We created the context in mounted()
            let context = descriptor.context;

            // Clear all notes from svg
            context.clear();

            // Redraw staves
            let staves = this._vex_draw_staves(context);

            let staveIndex = 0;

            let cursorNote = null;

            var ties = [];
            var renderQueue = [];
            var currentDuration = new Fraction(0);
            for(var i = 0; i < notes.length; i++){

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

                    cursorNote = newNote;
                    console.log(cursorNote);
                }

                renderQueue.push(newNote);

                currentDuration = currentDuration.add(notes[i].duration);
                
                if(currentDuration.compare(new Fraction(1)) == 0){
                    
                    this._vex_draw_voice(context, staves[staveIndex], renderQueue, {
                        ties: ties
                    });

                    for(let ff = 0; ff < renderQueue.length; ff++){
                        renderQueue[ff].attrs.el.setAttribute("onclick", "noteClicked("+i+")");
                    }

                    renderQueue = [];
                    ties = [];
                    staveIndex ++;
                    currentDuration = new Fraction(0);
                }

            }

            if(renderQueue.length > 0){
                // Draw the rest
                this._vex_draw_voice(context, staves[staveIndex + 1], renderQueue, {
                    ties: ties
                });
            }

        
            // Render the minimap rectangle
            if(descriptor.role == "minimap"){

                descriptor.context.rect(
                    this.info.lastMinimapBubbleX, 
                    0, 
                    this.info.lastMinimapBubbleW, 
                    this.info.barHeight, {
                    class: descriptor.bubble_class,
                    fill: "red",
                    opacity: 0.5
                });
            }

            
            this._cursor_rendered(descriptor, cursorNote);

        },

        

        render(notes, cursor) {
            for(var i = 0; i < this.VF.el_ids.length; i++){
                this._render_context(this.VF.el_ids[i], notes, cursor);
            }
        }
    },
    mounted(){

        // INIT

        // VexFlow Magic
        let VF = Vex.Flow;

        for(let idx_context = 0; idx_context < this.VF.el_ids.length; idx_context++)
        {
            var div = document.getElementById(this.VF.el_ids[idx_context].id)
            let renderer = new VF.Renderer(div, VF.Renderer.Backends.SVG);

            // Size our svg:
            renderer.resize(
                this.info.width,
                this.info.height,
            );

            this.VF.el_ids[idx_context].context = renderer.getContext();
        }

    },

}
</script>
