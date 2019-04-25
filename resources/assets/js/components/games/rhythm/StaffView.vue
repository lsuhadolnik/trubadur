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
        height: 160px
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
                height: 67,
                barWidth: window.innerWidth,
                barHeight: 67,
                barOffsetY: 19,

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
                x_coords.push(Math.round((e.getClientRects()[0].x + zoomScrollLeft))); // Does not work on iOS, works elsewhere
                //x_coords.push(Math.round((e.getBBox().x + zoomScrollLeft))); // DOES NOT work
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

            //var beams = VF.Beam.applyAndGetBeams(voice);

            var formatter = new VF.Formatter();
            formatter.joinVoices([voice]);
            formatter.format([voice], maxNotesWidth);
            

            voice.draw(context, stave);
            
            // Draw the beams:
            /*beams.forEach(function(beam){
                beam.setContext(context).draw();
            });*/

            
        },

        _vex_draw_optionals: function(context, events){

            if(!events){
                return;
            }

            events.forEach(function(opt){
                opt.setContext(context).draw();
            });

        },


        // Nova logika
        // Najprej naj bo na sceni en stave
        // Potem ko se note dodajajo, naj se dodajajo tudi črte


        _vex_draw_staves: function(context, barInfo){
            
            var staveCount = barInfo.length;

            let staves = [];
            
            let startAtX = 0;

            for(let idx_bar = 0; idx_bar < barInfo.length; idx_bar++){

                let thisBar = barInfo[idx_bar];

                let stave = new VF.Stave(
                    startAtX,   // X
                    -this.info.barOffsetY,           // Y
                    thisBar.width              // Width
                );

                startAtX += thisBar.width;

                staves.push(stave);

                // If this is the first stave
                if(idx_bar == 0){
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

            //let staveIndex = 0;
            let cursorNote = null;

            let batches = [];
            let barInfo = [];

            var ties = [];
            var tuplets = [];
            var renderQueue = [];
            //var currentDuration = new Fraction(0);

            let allStaveNotes = [];

            let latestNoteIndex = 0;
            let lastNoteIndex = -1;

            let firstTupletNoteIdx = -1;

            for(var i = 0; i < notes.length; i++){


                var thisNote = notes[i];

                if(thisNote.type != "bar"){
                    lastNoteIndex = latestNoteIndex;
                    latestNoteIndex = i;
                }

                // Bye bye, false note
                if(!thisNote) { continue; }

                // Handle notes and rests
                let newNote = new StaveNote(
                    {
                        clef: "treble", 
                        keys: ["g/4"], 
                        duration: thisNote.symbol
                    }
                );

                // Omogoči stiliziranje not
                // V zapis lahko zdaj daš objekt style in noter recimo informacije o barvi...
                if(thisNote.style){
                    newNote.setStyle(thisNote.style);
                }
                if(thisNote.overwrite){
                    newNote.setStyle({fillStyle:"blue", strokeStyle: "blue"});
                }

                // Handle dots
                if(thisNote.dot){
                    newNote.addDot(0); // enako je tudi newNote.addDotToAll()
                }
                
                // Get the note the cursor will stick to
                if(i + 1 == cursor.position){
                    cursorNote = newNote;
                }

                allStaveNotes.push(newNote);
                
                if(thisNote.type != "bar")
                    renderQueue.push(newNote);

                if(thisNote.type == "bar"){
                    newNote.setStyle({fillStyle: "transparent", strokeStyle: "transparent"});
                }

                if(thisNote.tie && i > 0){

                    // tie is:
                    //  - this note + last note
                    ties.push(new VF.StaveTie({
                        first_note: allStaveNotes[lastNoteIndex],
                        last_note:  allStaveNotes[latestNoteIndex],
                        first_indices: [0],
                        last_indices:  [0]
                    }));

                }

                /*if(thisNote.tuplet_from >= 0){
                    tuplets.push(new Vex.Flow.Tuplet(allStaveNotes.slice(thisNote.tuplet_from, thisNote.tuplet_to), {
                        bracketed: true, rationed: false, num_notes: thisNote.tuplet_type
                    }));
                }*/

                if(thisNote.in_tuplet){
                    if(firstTupletNoteIdx == -1){
                        firstTupletNoteIdx = i;
                    }
                }else{
                    firstTupletNoteIdx = -1;
                }

                if(thisNote.tuplet_end){
                    tuplets.push(new Vex.Flow.Tuplet(allStaveNotes.slice(firstTupletNoteIdx, i + 1), {
                        bracketed: true, rationed: false, num_notes: thisNote.tuplet_type
                    }));
                    firstTupletNoteIdx = -1;
                }

                

                /* The old auto-bar logic 
                currentDuration = currentDuration.add(notes[i].duration);
                
                // Kaj naredi:
                // => currentDuration.compare(new Fraction(1)) == 0 
                //    Ko je dovolj not za en takt, ga izrišem.
                // => !(staveIndex + 1 == staves.length && i + 1 < notes.length)
                //    V primeru da je not preveč, takta ne izrišem, ampak prihranim za zadnjega.

                //if(currentDuration.compare(new Fraction(this.bar.num_beats, this.bar.base_note)) >= 0 && !(staveIndex + 1 == staves.length && i + 1 < notes.length)){                  
                if(currentDuration.compare(new Fraction(this.bar.num_beats, this.bar.base_note)) >= 0){                      
                  
                    // Not yet
                    // this._vex_draw_voice(context, staves[staveIndex++], renderQueue);
                    batches.push(renderQueue);
                    //staveIndex++;

                    renderQueue = [];
                    currentDuration = new Fraction(0);
                }
                */

               /* the new manual bar logic */
               if(notes[i].type == "bar"){
                  
                    batches.push(renderQueue);
                    renderQueue = [newNote];

                }

            }

            // If there are still some notes left 
            // Happens if the bar is incomplete
            // sum(durations) != 1
            // Not only if less than 1 (incomplete bar)
            // but also if the bar overflows (more notes than possible...) - all notes will fit into the last bar... 
            if(renderQueue.length > 0){
                // Draw the rest
                //this._vex_draw_voice(context, staves[staveIndex], renderQueue);
                batches.push(renderQueue);
            }

            this._vex_render_batches(context, batches, [ties, tuplets]);
            
        
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
                if(ccNote.in_tuplet && !ccNote.hasOwnProperty("tuplet_end")){
                    // Ni na zadnji noti triole
                    this.cursor.in_tuplet = true;

                }
                else{
                    // Je na zadnji noti triole
                    this.cursor.in_tuplet = false;
                }
            }else{
                this.cursor.in_tuplet = false;
            }


            let n = this.cursor.position;
            if(notes.length > n && notes[n].in_tuplet && notes[n].type != "bar"){
                this.cursor.tuplet_type = notes[n].duration.d / notes[n].tuplet_type;
                //alert("HELLO! IN TUPLET. "+this.cursor.tuplet_type);
            }


        },

        _vex_render_batches(context, batches, optionals){

            //debugger;
            // Redraw staves
            var barInfo = [];

            var widthLeft = this.info.width;

            batches.forEach(batch => {
                var maxNotesWidth = Math.min(
                    // Give equal space to each note
                    batch.length * this.info.meanNoteWidth + 5, 
                    // Until there are too many notes to fit. 
                    // Then use maximum width and leave some space at the end
                    this.info.barWidth - this.info.meanNoteWidth
                );

                widthLeft -= maxNotesWidth;
                
                barInfo.push({
                    width: maxNotesWidth
                });    
            });

            // Initial empty bar
            if(widthLeft > 0){
                barInfo.push({
                    width: widthLeft
                });
            }

            let staves = this._vex_draw_staves(context, barInfo);

            for(var i = 0; i < batches.length; i++){
                this._vex_draw_voice(context, staves[i], batches[i]);
            }
            
            // Draw ties and tuplets
            optionals.forEach(opt => {
                this._vex_draw_optionals(context, opt);    
            });

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

        sR.onmousedown = function(e){
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
