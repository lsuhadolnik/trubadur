<template>

    <div class="rhythm-game__staff">
        
        <div class="rhythm-game__staff__first-row">
            <div id="first-row" ></div>
        </div>
        
        <div class="rhythm-game__staff__second-row">
            <div id="second-row"></div>
        </div>
        

        
        <!--height: <input class="BPM-slider" type="range" :min="10" :max="100" step="1" v-model="info.height" v-on:mousemove="force_redraw()"> {{info.height}}
        barHeight: <input class="BPM-slider" type="range" :min="10" :max="100" step="1" v-model="info.barHeight" v-on:mousemove="force_redraw()"> {{info.barHeight}}
        barOffsetY: <input class="BPM-slider" type="range" :min="10" :max="100" step="1" v-model="info.barOffsetY" v-on:mousemove="force_redraw()"> {{info.barOffsetY}}
        zoomViewHeight: <input class="BPM-slider" type="range" :min="10" :max="200" step="1" v-model="CTX.zoomview.containerHeight" v-on:mousemove="force_redraw()"> {{info.barOffsetY}}
        --> 
    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';

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
        height: 176px;
        /*scroll-behavior: smooth;
        -webkit-scroll-behavior: smooth;*/

        @include breakpoint-phone-landscape { height: 150px; }
    }

    #second-row {
        -webkit-transform: scale(2) translate(25%, 25%);
        transform: scale(2) translate(25%, 25%);
    }


</style>


<script>

import Vex from 'vexflow'
import RhythmRenderUtilities from './rhythmRenderUtilities'
let RU = new RhythmRenderUtilities();

var Fraction = require('fraction.js');
let util = require('./rhythmUtilities');

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
                height: 75,
                barWidth: window.innerWidth,
                barHeight: 75,
                barOffsetY: 16,

                zoomViewContainerHeight: 176,

                // Determines how much pixels 
                // an average note occupies.
                // Used to space notes evenly
                //meanNoteWidth: 60,
                //meanNoteWidth: 30,

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
                    viewHeight: 60
                }, 
                zoomview: {
                    id: "second-row",
                    role: "zoomview",
                    //containerHeight: 176,  
                },
            }
        }
    },

    methods: {

        reset() {
            document.getElementById("second-row").parentElement.scrollTo(0,0)
        },

        note_clicked: function(Xoffset){

            let closest = this._get_closest_note(Xoffset);
            if(closest.idx >= 0){
                
                this.cursor.position = closest.idx + 1;
                this.cursor_moved();

                this._save_scroll();
                this.$parent.notes._call_render()
                this._restore_scroll();
            }

        },

        _handle_second_selection_tap: function(idx){

            if(!this.cursor.selectionMode){ return; }

            if(idx == this.cursor.selection.base){
                this.cursor.selection.from = idx
                this.cursor.selection.to = idx;
            }

            if(idx < this.cursor.selection.base){
                this.cursor.selection.from = idx
                this.cursor.selection.to = this.cursor.selection.base;
            }

            if(idx > this.cursor.selection.base){
                this.cursor.selection.from = this.cursor.selection.base;
                this.cursor.selection.to = idx;
            }

        },

        _get_closest_note: function(Xoffset){

            let zoomView = document.getElementById("second-row").parentNode;
            let x = Xoffset + zoomView.scrollLeft;

            // xcoords are calculated after render
            return RU._min_dist(this.CTX["zoomview"].x_coords, x);

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

        cursor_moved(){

            // Disable invalid selection
            // If the cursor is at 0, the selection becomes just a purple bar - useless...
            if(this.cursor.position == 0 && this.cursor.selectionMode){
                this.cursor.position = 1;
            }

            let pos = this.cursor.position - 1;
            this._handle_second_selection_tap(pos);

        },

        _handle_tuplet_editing_mode_change(pos){

            let note = this.$parent.notes.currentNote();
            if(note.in_tuplet){
                this.$parent.notes.enable_tuplet_editing();
            }else{
                this.cursor.editing_tuplet = false;
            }

        },

        // FIX THIS CRAP ASAP
        _cursor_rendered(cursorNode, descriptor, notes, cursorNoteIndex){
            

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

                let cursorOffset = 0;
                let currentNoteValue = notes[this.cursor.position - 1].value;
                switch (currentNoteValue) {
                    case 1:  cursorOffset = 22; break;
                    case 2:  cursorOffset = 22;  break;
                    case 4:  cursorOffset = 22;  break;
                    case 8:  cursorOffset = 15;  break;
                    case 16: cursorOffset = 10;  break;
                    case 32: cursorOffset = 8;  break;
                    default: cursorOffset = 5;  break;
                }

                //alert(this.info.cursor.cursorMargin+" : "+cursorOffset);

                //let v = ((startX + sR.scrollLeft)/zoomScrollWidth)*bubbleScrollWidth + this.info.cursor.cursorMargin;
                let v = ((startX + sR.scrollLeft)/zoomScrollWidth)*bubbleScrollWidth + cursorOffset;

                this._set_cursor_position(v);
            }
            
            // Here I could cancel unnecessary scroll if the cursor was still visible
            // But I disabled that


            this.scrolled(startX - screenWidth*0.5);

        },

        _render_context(descriptor, notes, cursor){



            if(window.innerHeight <= 600){
                // Size the svg: - PLEASE MOVE THIS LOGIC SOMEWHERE ELSE! THANKS!
                descriptor.renderer.resize(
                    this.info.width,
                    65,
                );
            }else {
                // Size our svg:
                descriptor.renderer.resize(
                    this.info.width,
                    this.info.height,
                );
            }

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
            descriptor.rendered = [];

            let allStaveNotes = [];

            let latestNoteIndex = 0;
            let lastNoteIndex = -1;

            let firstTupletNoteIdx = -1;

            let currentBatchWidth = 0;

            for(var i = 0; i < notes.length; i++){

                var thisNote = notes[i];

                if(thisNote.type != "bar"){
                    lastNoteIndex = latestNoteIndex;
                    latestNoteIndex = i;
                }

                // Bye bye, false note
                if(!thisNote) { continue; }

                // Handle notes and rests
                let symbol = thisNote.value + "";
                if(thisNote.type == "r")
                    symbol += "r";

                let newNote = new StaveNote(
                    {
                        clef: "treble", 
                        keys: ["g/4"], 
                        duration: symbol
                    }
                );

                switch (thisNote.value) {
                    case 1:  currentBatchWidth += 100; break;
                    case 2:  currentBatchWidth += 70;  break;
                    case 4:  currentBatchWidth += 40;  break;
                    case 8:  currentBatchWidth += 40;  break;
                    case 16: currentBatchWidth += 30;  break;
                    case 32: currentBatchWidth += 30;  break;
                    default: currentBatchWidth += 20;  break;
                }

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
                
                if(thisNote.type != "bar"){
                    renderQueue.push(newNote);
                    descriptor.rendered.push(newNote);
                }
                    

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


                if(thisNote.in_tuplet){
                    if(firstTupletNoteIdx == -1){
                        firstTupletNoteIdx = i;
                    }
                }else{
                    firstTupletNoteIdx = -1;
                }

                if(thisNote.tuplet_end){

                    let tuplet_type = thisNote.tuplet_type;

                    tuplets.push(new Vex.Flow.Tuplet(allStaveNotes.slice(firstTupletNoteIdx, i + 1), {
                        bracketed: true, ratioed: false, num_notes: tuplet_type, notes_occupied: tuplet_type
                    }));
                    firstTupletNoteIdx = -1;
                }


               /* the new manual bar logic */
               if(notes[i].type == "bar"){
                  
                    batches.push({notes:renderQueue, width:currentBatchWidth});
                    renderQueue = [newNote];
                    currentBatchWidth = 0;

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
                batches.push({notes:renderQueue, width:currentBatchWidth});
            }

            // Finally render everything
            RU._vex_render_batches(context, batches, [ties, tuplets], {
                bar: this.bar,
                barOffsetY: this.info.barOffsetY,
                width: this.info.width
            });

            // set CTX.zoomview.x_coords property
            this.retrieveXCoords(descriptor);


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
            

            if(this.cursor.selectionMode){
                this.draw_selection_bubble(descriptor)
            }else{
                this._cursor_rendered(cursorNote, descriptor, notes);
            }

            
            

            // Move this logic somewhere else
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


        },

        retrieveXCoords(ctx){

            /// THIS FREAKING WORKS EVERYWHERE!!! 💪💪💪💪💪
            ctx.x_coords = [];
            ctx.render_widths = [];
            
            if(!ctx.rendered) return;

            ctx.rendered.forEach(note => {
                ctx.x_coords.push(Math.round(note.getAbsoluteX() * 2));
                ctx.render_widths.push(note.width);
            });

        },

        render(notes, cursor) {
            for(var key in this.CTX){

                let ctx = this.CTX[key];

                if(ctx.containerHeight){
                    let container = document.getElementById(ctx.id).parentElement;
                    container.style.height = ctx.containerHeight+"px";
                }

                if(ctx.viewHeight){
                    let view = document.getElementById(ctx.id);
                    view.style.height = ctx.viewHeight+"px";
                }

                this._render_context(ctx, notes, cursor);
            }
        },

        draw_selection_bubble(ctx){

            if(!this.cursor.selection){
                return;
            }

            let fromX = ctx.x_coords[this.cursor.selection.from] / 2;
            let toX = ctx.x_coords[this.cursor.selection.to] / 2;
            let width = toX - fromX + ctx.render_widths[ctx.render_widths.length - 1];

            let bodyColor = "blue";
            let baseColor = "red";
            let leftColor = bodyColor, rightColor = bodyColor;
            if(this.cursor.selection.base > this.cursor.selection.from){
                leftColor = baseColor; 
            }
            else{ rightColor = baseColor; }

            let handlesWidth = 3;
            let offset = 10;
            let heightOffset = 10;

            // RENDER SELECTION BUBBLE
            ctx.context.rect(
                fromX - offset,  heightOffset, 
                width + 2 * offset,  this.info.barHeight - heightOffset, 
                {
                    class: "notesSelection_body",
                    fill: bodyColor, opacity: 0.4
                }
            );

            // RENDER LEFT HANDLE
            ctx.context.rect(
                fromX - offset,  heightOffset, 
                handlesWidth,  this.info.barHeight - heightOffset, 
                {
                    class: "notesSelection_left_handle",
                    fill: leftColor, opacity: 0.4
                }
            );

            // RENDER RIGHT HANDLE
            ctx.context.rect(
                fromX + width + offset - handlesWidth,  heightOffset, 
                handlesWidth,  this.info.barHeight - heightOffset, 
                {
                    class: "notesSelection_right_handle",
                    fill: rightColor, opacity: 0.4
                }
            );


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
