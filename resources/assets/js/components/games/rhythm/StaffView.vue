<template>

    <div class="rhythm-game__staff">
        
        <div class="rhythm-game__staff__first-row">
            <div id="first-row" ></div>
        </div>
        
        <div class="rhythm-game__staff__second-row">
            <div id="second-row"></div>
        </div>
        
    </div>

</template>

<style lang="scss" scoped>

    .rhythm-game__staff__second-row {
        /*scroll-behavior: smooth;
        -webkit-scroll-behavior: smooth;*/
    }

    #first-row {
        transform: scale(0.5) translate(-50%, 0);
    }

    .rhythm-game__staff__second-row {
        /*background:red;*/
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
        overflow-scrolling: touch;
        height: 200px
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
        'bar', 'cursor', 'staveCount'
    ],

    data () {
        return {

            info: {
                width: 2*(window.innerWidth),
                height: 80,
                barWidth: window.innerWidth,
                barHeight: 80,
                barOffsetY: 10,

                // Determines how much pixels 
                // an average note occupies.
                // Used to space notes evenly
                meanNoteWidth: 60,

                maxStaveWidth: 320,

                bubble_class: "minimap-bubble",
                lastMinimapBubbleX: 0,
                lastMinimapBubbleW: 0,

                minimap_in_click: false,

                scrollBuffer: {
                    minimapX: 0,
                    scrollX: 0
                },

                cursor: {
                    cursorBarClass: "cursor-bar",
                    cursorMargin: 22
                }

            },

            CTX: {
                minimap: {
                    id: "first-row",
                    role: "minimap",
                }, 
                zoomview: {
                    id: "second-row",
                    role: "zoomview"
                }
            }
        }
    },
    methods: {

        note_clicked: function(Xoffset){

            let closest = this._get_closest_note(Xoffset);
            if(closest.idx >= 0){
                this.cursor.position = closest.idx + 1;
                this._save_scroll();
                this.$parent.notes._call_render()
                this._restore_scroll();
            }

        },

        _get_closest_note: function(Xoffset){

            let zoomView = document.getElementById("second-row").parentNode;
            let screenWidth = window.innerWidth;
            let zoomScrollWidth = zoomView.scrollWidth;
            
            let zoomScrollLeft = zoomView.scrollLeft;

            let x = Xoffset + zoomScrollLeft;

            console.log("screenWidth: "+screenWidth+", zoomScrollWidth: "+zoomScrollWidth+", zoomScrollLeft: "+zoomScrollLeft+", x: "+x);

            var x_coords = [];
            zoomView.querySelectorAll(".vf-note").forEach(function(e) {
                x_coords.push(Math.round((e.getClientRects()[0].x + zoomScrollLeft)));
            });

            console.log(x_coords)

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

        },

        _get_scroll_data: function(){

            // Vrni: 
            // - Screen Width
            // - Zoom Width
            // ...

            let zoomView = document.getElementById("second-row").parentNode;
            let bubble = document.querySelector("."+this.info.bubble_class);

            return {

                screenWidth: window.innerWidth,

                zoomView: zoomView,
                zoomScrollWidth: zoomView.scrollWidth,
                zoomScrollLeft: zoomView.scrollLeft,

                bubble: bubble,
                bubbleWidth: bubble.getAttribute("width"),
                bubbleX: bubble.getAttribute("x")
            }

        },

        _save_scroll: function(){

            let zoomView = document.getElementById("second-row").parentNode;
            this.info.scrollBuffer.scrollX = zoomView.scrollLeft;

            let bubble = document.querySelector("."+this.info.bubble_class);
            this.info.scrollBuffer.minimapX = bubble.getAttribute('x');

        },

        _restore_scroll: function() {
            let zoomView = document.getElementById("second-row").parentNode;
            zoomView.scroll(this.info.scrollBuffer.scrollX, 0);

            let bubble = document.querySelector("."+this.info.bubble_class);
            bubble.setAttribute('x', this.info.scrollBuffer.minimapX);
        },

        scrolled: function(x, hasScrolled){

            // Sprejme x koordinato začetka odmika ali balončka
            let minimap = document.getElementById("first-row");
            let bubble = document.querySelector("."+this.info.bubble_class);
            let zoomView = document.getElementById("second-row").parentNode;
            if(!hasScrolled)
                zoomView.scroll(x,0);

            let zoomScrollWidth = zoomView.scrollWidth;
            let bubbleScrollWidth = minimap.scrollWidth;
            let bubbleWidth = bubble.getAttribute("width");

            let bubbleX = (x/zoomScrollWidth)*bubbleScrollWidth;

            bubbleX = Math.max(0, bubbleX);
            bubbleX = Math.min(bubbleScrollWidth - bubbleWidth, bubbleX)

            bubble.setAttribute("x", bubbleX);
            this.info.lastMinimapBubbleX = bubbleX;


        },

        _set_bubble_width: function(w){
            let rect = document.querySelector("."+this.info.bubble_class);
            if(rect)
                rect.setAttribute("width", w);
            
            this.info.lastMinimapBubbleW = w;
        },

        _set_cursor_position: function(x){
            let cE = document.getElementsByClassName(this.info.cursor.cursorBarClass);
            for(var idx_cursor = 0; idx_cursor < cE.length; idx_cursor++){
                cE[idx_cursor].setAttribute('x', x);
            }
        },

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
            var maxNotesWidth = Math.min(
                // Give equal space to each note
                renderQueue.length * this.info.meanNoteWidth, 
                // Until there are too many notes to fit. 
                // Then use maximum width and leave some space at the end
                this.info.barWidth - this.info.meanNoteWidth
            );

            var formatter = new VF.Formatter().format([voice], maxNotesWidth);

            // Render voice
            // beams.forEach(function(b) {b.setContext(context).draw()})

            voice.draw(context, stave);
            
            // Generate beams
            /*var beams = VF.Beam.generateBeams(renderQueue,  {
                beam_rests: true,
                show_stemlets: true
            });
            // Draw the beams:
            beams.forEach(function(beam){
                beam.setContext(context).draw();
            });*/

            // Draw optionals...
            if(optionals) {

                if(optionals.ties) {
                    // Draw the ties
                    optionals.ties.forEach(function(t) {t.setContext(context).draw()})
                }

                if(optionals.tuplets) {
                    // Draw the tuplets:
                    optionals.tuplets.forEach(function(tuplet){
                        tuplet.setContext(context).draw();
                    });
                }

            }
        },

        _vex_draw_optionals: function(context, events){

            if(!events){
                return;
            }

            events.forEach(function(opt){
                opt.setContext(context).draw();
            });

        },

        _vex_draw_staves: function(context){
            
            let staves = [];

            for(let idx_stave = 0; idx_stave < this.staveCount; idx_stave++){

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
                
                // Connect it to the rendering context
                stave.setContext(context);

            }

            let connectors = [];

            for(let idx_stave = 1; idx_stave < this.staveCount; idx_stave++){
                var connector = new VF.StaveConnector(staves[idx_stave - 1], staves[idx_stave]);
                connector.setType(VF.StaveConnector.type.SINGLE);
                connector.setContext(context);
                connectors.push(connector);
            }

            // Draw the first stave
            staves[0].draw();

            for(let idx_stave = 1; idx_stave < this.staveCount; idx_stave++){
                staves[idx_stave].draw();
                connectors[idx_stave - 1].draw();
            }

            return staves;
        },

        _minimap_clicked(x) {

            let v = document.getElementById("second-row").parentNode;

            // Content width
            let contentWidth = v.scrollWidth;

            // Screen width
            let screenWidth = window.innerWidth;

            // Touch X
            let touchX = x;

            let sDoSomeMath = (touchX / screenWidth) * contentWidth - screenWidth / 2;

            this.scrolled(sDoSomeMath);

        },

        _cursor_rendered(cursorNode, descriptor){
            

            let screenWidth = window.innerWidth;
            let sR = document.getElementById("second-row").parentElement;
            var scrollWidth = sR.scrollWidth;

            // ZOOM-BREAK
            var minimapWidth = screenWidth * 2;

            let bubbleW = (screenWidth/scrollWidth) * minimapWidth;

            // No cursor note
            // Cursor is right at the start
            if(!cursorNode){
                
                this._set_cursor_position(20)
                return;
            }


            let bbox = cursorNode.attrs.el.getElementsByClassName("vf-note")[0].getBoundingClientRect();
            //let bbox = cursorNode.attrs.el.getClientRects()[0];
            //let bbox = cursorNode.attrs.el.getElementsByClassName("vf-note")[0].getClientRects()[0];
            
            let startX = bbox.left + bbox.width / 2;
            //let startX = bbox.x;

            // To sicer dela, ampak ne na iPhonu
            // let bbox = cursorNode.attrs.el.getElementsByClassName("vf-note")[0].getClientRects()[0];
            // let startX = bbox.x;


            // ZOOM-BREAK
            this._set_bubble_width(bubbleW);

            if(descriptor.role == "zoomview"){
                
                let zoomScrollWidth = scrollWidth;
                let bubbleScrollWidth = minimapWidth;

                let v = ((startX + sR.scrollLeft)/zoomScrollWidth)*bubbleScrollWidth + this.info.cursor.cursorMargin;

                this._set_cursor_position(v);
            }
            
            // Here I could cancel unnecessary scroll if the cursor was still visible
            // But I disabled that


            this.scrolled(startX - screenWidth*0.5);

        },

        _render_context(descriptor, notes, cursor){


            // Render onto n bars. Assumes no bar overlapping...

            // Size our svg:
            descriptor.renderer.resize(
                this.info.width,
                this.info.height,
            );

            // element from CTX object
            // We created the context in mounted()
            let context = descriptor.context;

            // Clear all notes from svg
            context.clear();

            // Redraw staves
            let staves = this._vex_draw_staves(context);

            let staveIndex = 0;
            let cursorNote = null;

            let currentStaveNoteIdx = 0;

            var ties = [];
            var tuplets = [];
            var renderQueue = [];
            var currentDuration = new Fraction(0);

            let allStaveNotes = [];

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


                // Get the note the cursor will stick to
                if(i + 1 == cursor.position){
                    cursorNote = newNote;
                }

                allStaveNotes.push(newNote);
                renderQueue.push(newNote);
                currentStaveNoteIdx ++;

                if(notes[i].tie && i > 0){
                
                    // tie is:
                    //  - this note + last note
                    ties.push(new VF.StaveTie({
                        first_note: allStaveNotes[i - 1],
                        last_note:  allStaveNotes[i],
                        first_indices: [0],
                        last_indices:  [0]
                    }));

                }

                if(notes[i].tuplet_from >= 0){
                    tuplets.push(new Vex.Flow.Tuplet(allStaveNotes.slice(notes[i].tuplet_from, notes[i].tuplet_to), {
                        bracketed: true, rationed: false, num_notes: notes[i].tuplet_type
                    }));
                }

                currentDuration = currentDuration.add(notes[i].duration);
                
                if(currentDuration.compare(new Fraction(1)) == 0){

                    this._vex_draw_voice(context, staves[staveIndex++], renderQueue);

                    renderQueue = [];
                    currentDuration = new Fraction(0);
                }

            }

            // If there are still some notes left 
            // Happens if the bar is incomplete
            // sum(durations) != 1
            // Not only if less than 1 (incomplete bar)
            // but also if the bar overflows (more notes than possible...) - all notes will fit into the last bar... 
            if(renderQueue.length > 0){
                // Draw the rest
                this._vex_draw_voice(context, staves[staveIndex ++], renderQueue);
            }

            this._vex_draw_optionals(context, ties);
            this._vex_draw_optionals(context, tuplets);

        
            // Render the minimap rectangle
            if(descriptor.role == "minimap"){

                descriptor.context.rect(
                    this.info.lastMinimapBubbleX, 
                    0, 
                    this.info.lastMinimapBubbleW, 
                    this.info.barHeight, {
                    class: this.info.bubble_class,
                    fill: "red",
                    opacity: 0.5
                });
            }

            // RENDER CURSOR BAR
            descriptor.context.rect(
                150, 
                0, 
                2, 
                this.info.barHeight, {
                class: this.info.cursor.cursorBarClass,
                fill: "green",
                opacity: 0.5
            });
            
            this._cursor_rendered(cursorNote, descriptor);

            // Nastavi lastnost cursor.in_tuplet
            // S tem skrijem gumbe takrat, ko sem v trioli, 
            /// zato da se ne dogajajo čudne stvari
            if(this.cursor.position - 1 >= 0 && notes.length > this.cursor.position - 1){
                let ccNote = notes[this.cursor.position - 1];
                if(ccNote.in_tuplet && !ccNote.hasOwnProperty("tuplet_from")){
                    this.cursor.in_tuplet = true;
                }
                else{
                    this.cursor.in_tuplet = false;
                }
            }else{
                this.cursor.in_tuplet = false;
            }

        },

        render(notes, cursor) {
            for(var key in this.CTX){
                this._render_context(this.CTX[key], notes, cursor);
            }
        },

        rerender_notes() {
            this.$parent.notes._call_render()
        },

        viewportResized() {

            this.info.width = 2*(window.innerWidth);
            this.info.barWidth   = window.innerWidth;
            
            this.rerender_notes()

        }

    },
    mounted(){

        // VexFlow Magic
        let VF = Vex.Flow;
        for(let ctx_key in this.CTX)
        {
            var div = document.getElementById(this.CTX[ctx_key].id)
            var renderer = new VF.Renderer(div, VF.Renderer.Backends.SVG);

            this.CTX[ctx_key].el = div;
            this.CTX[ctx_key].parentElement = div.parentElement;
            this.CTX[ctx_key].scrollElement = div.parentElement;
            this.CTX[ctx_key].renderer = renderer;
            this.CTX[ctx_key].context  = renderer.getContext();
        }

        // INIT
        var sR = this.CTX.zoomview.parentElement;
        var vue = this;
        sR.onscroll = function(e){
            vue.scrolled(sR.scrollLeft, true);
            //e.preventDefault();
            return false;
        }

        sR.onclick = function(e){
            vue.note_clicked(e.clientX);
        }

        var fR = this.CTX.minimap.parentElement;

        fR.ontouchmove = function(e){
            vue._minimap_clicked(e.touches[0].clientX)
        }

        fR.onmousedown = function(e){
            vue.info.minimap_in_click = true;
            vue._minimap_clicked(e.clientX);
        }
        fR.onmouseup = function(e){
            vue.info.minimap_in_click = false;
        }
        fR.onmousemove = function(e){
            if(vue.info.minimap_in_click)
                vue._minimap_clicked(e.clientX);
        }

        window.onresize = function(event) {
            vue.viewportResized();
        }

        window.onorientationchange = function(event) {
            vue.viewportResized();
        }

        

    },

}
</script>
