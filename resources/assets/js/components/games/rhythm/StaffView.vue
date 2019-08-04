<template>

    <div class="rhythm-game__staff">
        
        <slot />
        
    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';

    #first-row {
        transform: scale(0.5) translate(-50%, 0);
    }

    .rhythm-game__staff__second-row {
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
        overflow-scrolling: touch;
        height: 176px;

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

let util = require('./rhythmUtilities');

let VF = Vex.Flow;
let StaveNote = VF.StaveNote;
let Tuplet = VF.Tuplet;

export default {

    props: [
        'bar', 'staveCount', 'rerender', 'enabledContexts'
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

                scrollBuffer: {
                    minimapX: 0,
                    scrollX: 0
                },

                cursor: {
                    enabled: false,
                    cursorBarClass: "cursor-bar",
                    cursorMargin: 22
                },

                barnoteWidth: 40,

            },

            cursor: {
                position: 0,
                x: 0,
                in_tuplet: false,

                cursor_moved: this.cursor_moved,

                selection: null, 
                selectionMode: false,
                selectionSelected: false,

                clearSelection: this.clearSelection,
                toggleSelectionMode: this.toggleSelectionMode,

                editing_tuplet: false,
                editing_tuplet_index: -1
            },

            tempNotes: null, 

            CTX: {
                minimap: {
                    id: "first-row",
                    role: "minimap",
                    viewHeight: 60,
                    minimap_in_click: false,

                    renderSpecifics: function(render_context){
                        // this is the descriptor object
                        render_context.draw_minimap_bubble(this);
                    },

                    bubble: {
                        bubble_class: "minimap-bubble",
                        lastMinimapBubbleX: 0,
                        lastMinimapBubbleW: 0,
                    },

                    init: function(vue) {
                        var fR = this.parentElement;
                        var descriptor = this;

                        fR.ontouchmove = function(e){
                            vue._minimap_clicked(e.touches[0].clientX)
                        }

                        fR.onmousedown = function(e){
                            descriptor.minimap_in_click = true;
                            vue._minimap_clicked(e.clientX);
                        }

                        fR.onmouseup = function(e){
                            descriptor.minimap_in_click = false;
                        }

                        fR.onmousemove = function(e){
                            if(descriptor.minimap_in_click)
                                vue._minimap_clicked(e.clientX);
                        }
                    }
                }, 
                zoomview: {
                    id: "second-row",
                    role: "zoomview",
                    scale: 2,
                    //containerHeight: 176,  
                    init: function(vue) {
                        // this = descriptor object

                        var sR = this.parentElement;
                        sR.onscroll = function(e){
                            vue.scrolled(sR.scrollLeft, true);
                            return false;
                        }

                        sR.onmousedown = function(e){
                            vue.note_clicked(e.offsetX);
                        }

                    }
                },
            }
        }
    },

    methods: {

        reset() {
            this.CTX.zoomview.parentElement.scrollTo(0,0)
        },

        note_clicked: function(Xoffset){

            if(!this.info.cursor.enabled) return;

            let closest = this._get_closest_note(Xoffset);
            if(closest.idx >= 0){
                
                this.cursor.position = closest.idx + 1;
                this.cursor_moved();

                this._save_scroll();
                this._render_temp();
                this._restore_scroll();
            }

        },

        clearSelection(){

            this.cursor.selection = null;
            this.cursor.selectionSelected = false;
            this.cursor.selectionMode = false;

        },

        toggleSelectionMode(){

            if(this.cursor.selectionMode){

                this.clearSelection();

            }else{

                if(this.tempNotes.length == 0) {
                    return;
                }

                this.cursor.selectionMode = true;
                this.cursor.selectionSelected = false;
                
                let pos = this.cursor.position - 1;
                if(pos < 0){
                    pos = 0;
                }

                else if(pos > this.tempNotes.length){
                    pos = this.tempNotes.length - 1;
                }

                this.cursor.selection = {
                    base: pos,
                    from: pos,
                    to: pos
                }

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

            // xcoords are calculated after render
            return RU._min_dist(this.CTX["zoomview"].x_coords, Xoffset);

        },

        _save_scroll: function(){

            let zoomView = this.CTX.zoomview.parentElement;
            this.info.scrollBuffer.scrollX = zoomView.scrollLeft;

            if(this.CTX.minimap.enabled){
                let bubble = document.querySelector("."+this.CTX.minimap.bubble.bubble_class);
                this.info.scrollBuffer.minimapX = bubble.getAttribute('x');
            }
            
        },

        _restore_scroll: function() {
            
            let zoomView = this.CTX.zoomview.parentElement;
            zoomView.scroll(this.info.scrollBuffer.scrollX, 0);

            if(this.CTX.minimap.enabled){
                let bubble = document.querySelector("."+this.CTX.minimap.bubble.bubble_class);
                bubble.setAttribute('x', this.info.scrollBuffer.minimapX);
            }
        },

        scrolled: function(x, hasScrolled){

            let zoomView = this.CTX.zoomview.parentElement;
            let zoomScrollWidth = zoomView.scrollWidth;
            if(!hasScrolled)
                zoomView.scroll(x,0);


            if(this.CTX.minimap.enabled){
                // Sprejme x koordinato začetka odmika ali balončka
                let minimap = this.CTX.minimap.el;
                let bubble = document.querySelector("."+this.CTX.minimap.bubble.bubble_class);
                
                let bubbleScrollWidth = minimap.scrollWidth;
                let bubbleWidth = bubble.getAttribute("width");
                let bubbleX = (x/zoomScrollWidth)*bubbleScrollWidth;

                bubbleX = Math.max(0, bubbleX);
                bubbleX = Math.min(bubbleScrollWidth - bubbleWidth, bubbleX)

                bubble.setAttribute("x", bubbleX);
                this.CTX.minimap.bubble.lastMinimapBubbleX = bubbleX;
            }
            

        },

        _set_bubble_width: function(w){

            if(!this.CTX.minimap.enabled) return;

            let rect = document.querySelector("."+this.CTX.minimap.bubble.bubble_class);
            if(rect)
                rect.setAttribute("width", w);
            
            this.CTX.minimap.bubble.lastMinimapBubbleW = w;
        },

        _set_cursor_position: function(x){
            let cE = document.getElementsByClassName(this.info.cursor.cursorBarClass);
            for(var idx_cursor = 0; idx_cursor < cE.length; idx_cursor++){
                cE[idx_cursor].setAttribute('x', x);
            }
        },

        _minimap_clicked(x) {

            let v = this.CTX.zoomview.parentElement;

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
                150, 0, 2, 
                this.info.barHeight, {
                    class: this.info.cursor.cursorBarClass,
                    fill: "green",
                    opacity: 0.5
                }
            );

            let screenWidth = window.innerWidth;
            let sR = this.CTX.zoomview.parentElement;
            var scrollWidth = sR.scrollWidth;

            // ZOOM-BREAK
            var minimapWidth = screenWidth * 2;

            let bubbleW = (screenWidth/scrollWidth) * minimapWidth;

            // No cursor note
            // Cursor is right at the start
            // After all time signatures
            if(!cursorNode){

                let signatures = RU._construct_time_signature(this.bar);
                let offset = 30 * signatures.length;

                this._set_cursor_position(offset)
                return;
            }

            let cIdx = this.cursor.position - 1;
            let scale = descriptor.scale;
            let l = descriptor.x_coords[cIdx] * scale;
            let w = descriptor.render_widths[cIdx] * scale;
            let startX = l + w / 2;


            // ZOOM-BREAK
            if(descriptor.role == "minimap" && descriptor.enabled){
                this._set_bubble_width(bubbleW);
            }

            if(descriptor.role == "zoomview"){
                
                let zoomScrollWidth = scrollWidth;
                let bubbleScrollWidth = minimapWidth;

                let cursorOffset = 0;
                let currentNoteValue = notes[cIdx].value;
                switch (currentNoteValue) {
                    case 1:  cursorOffset = 22;  break;
                    case 2:  cursorOffset = 22;  break;
                    case 4:  cursorOffset = 22;  break;
                    case 8:  cursorOffset = 15;  break;
                    case 16: cursorOffset = 10;  break;
                    case 32: cursorOffset = 8;   break;
                    default: cursorOffset = 5;   break;
                }

                let v2 = ((startX + sR.scrollLeft)/zoomScrollWidth)*bubbleScrollWidth + cursorOffset;
                this._set_cursor_position(v2);
            }
            
            this.scrolled(startX - screenWidth*0.5);

            RU._check_cursor_in_tuplet(this.cursor, notes);

        },

        _generate_batches(notes) {

            let allStaveNotes = [];
            let latestNoteIndex = 0, lastNoteIndex = -1;

            let firstTupletNoteIdx = -1;

            // Set initial bar width
            let currentBatchWidth = 0;
            
            //let staveIndex = 0;
            let cursorNote = null;

            let batches = [], barInfo = [];

            var ties = [], tuplets = [], renderQueue = [];


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

                let newNote = null;
                if(thisNote.type == "bar"){
                    newNote = new StaveNote({ clef: "treble", keys: ["g/4"], duration: "1r" });
                }else {
                    newNote = new StaveNote({ clef: "treble", keys: ["g/4"], duration: symbol });
                }

                if(thisNote.type != "bar") switch (thisNote.value) {
                    case 1:  currentBatchWidth += 100; break;
                    case 2:  currentBatchWidth += 70;  break;
                    case 4:  currentBatchWidth += 40;  break;
                    case 8:  currentBatchWidth += 40;  break;
                    case 16: currentBatchWidth += 30;  break;
                    case 32: currentBatchWidth += 30;  break;
                    default: currentBatchWidth += 20;  break;
                }

                // Handle dots
                if(thisNote.dot){
                    newNote.addDot(0); // enako je tudi newNote.addDotToAll()
                }
                
                // Get the note the cursor will stick to
                if(this.info.cursor.enabled && i + 1 == this.cursor.position){
                    cursorNote = newNote;
                }

                if(thisNote.type == "bar"){
                    newNote.setStyle({fillStyle: "transparent", strokeStyle: "transparent"});
                }

                allStaveNotes.push(newNote);
                
                if(thisNote.type != "bar"){
                    renderQueue.push(newNote);    
                }

                
                if(thisNote.tie && i > 0){

                    // tie is:
                    //  - this note + last note
                    ties.push(new VF.StaveTie({
                        first_note: allStaveNotes[lastNoteIndex],
                        last_note:  allStaveNotes[latestNoteIndex],
                        first_indices: [0], last_indices:  [0]
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

                    tuplets.push(new VF.Tuplet(allStaveNotes.slice(firstTupletNoteIdx, i + 1), {
                        bracketed: true, num_notes: tuplet_type.num_notes , notes_occupied: tuplet_type.in_space_of
                    }));
                    firstTupletNoteIdx = -1;
                }


               /* the new manual bar logic */
               if(notes[i].type == "bar"){

                    batches.push({notes:renderQueue, width:currentBatchWidth});
                    renderQueue = [newNote];
                    currentBatchWidth = this.info.barnoteWidth;
                }

            }

            // Draw the rest
            if(renderQueue.length > 0){
                batches.push({notes:renderQueue, width:currentBatchWidth});
            }

            return {
                batches: batches,
                optionals: [ties, tuplets],
                allStaveNotes: allStaveNotes,
                cursorNote: cursorNote
            }

        },

        _render_context(descriptor, notes){

            // element from CTX object
            // We created the context in mounted()
            let context = descriptor.context;

            // Clear all notes from svg
            context.clear();
            descriptor.rendered = [];

            // There comes the fattie
            let data = this._generate_batches(notes);
            descriptor.rendered = data.allStaveNotes;

            // Finally render everything
            RU._vex_render_batches(context, data.batches, data.optionals, {
                bar: this.bar,
                barOffsetY: this.info.barOffsetY,
                width: this.info.width
            }, notes);

            // set CTX.zoomview.x_coords property
            this.retrieveXCoords(descriptor);

            if(descriptor.renderSpecifics){
                descriptor.renderSpecifics(this, descriptor)
            }

            // Render cursor
            if(this.info.cursor.enabled) {
                if(this.cursor.selectionMode) {
                    this.draw_selection_bubble(descriptor)
                } else {
                    this._cursor_rendered(data.cursorNote, descriptor, notes);
                }
            }

        },

        retrieveXCoords(ctx){

            ctx.x_coords = [];
            ctx.render_widths = [];
            
            if(!ctx.rendered) return;

            ctx.rendered.forEach(note => {
                ctx.x_coords.push(Math.round(note.getAbsoluteX()));
                ctx.render_widths.push(note.width);
            });

        },

        _render_temp() {
            this.enabledContexts.forEach(key => {

                let ctx = this.CTX[key];

                if(ctx.containerHeight){
                    let container = document.getElementById(ctx.id).parentElement;
                    container.style.height = ctx.containerHeight+"px";
                }

                if(ctx.viewHeight){
                    let view = document.getElementById(ctx.id);
                    view.style.height = ctx.viewHeight+"px";
                }

                if(window.innerHeight <= 600){
                    // Size the svg: - PLEASE MOVE THIS LOGIC SOMEWHERE ELSE! THANKS!
                    ctx.renderer.resize(
                        this.info.width,
                        65,
                    );
                }else {
                    // Size our svg:
                    ctx.renderer.resize(
                        this.info.width,
                        this.info.height,
                    );
                }

                this._render_context(ctx, this.tempNotes);
            });
        },

        render(notes) {
            this.tempNotes = notes;
            this._render_temp();
        },

        draw_selection_bubble(ctx){

            if(!this.cursor.selection){
                return;
            }

            let fromX = ctx.x_coords[this.cursor.selection.from];
            let toX = ctx.x_coords[this.cursor.selection.to];
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

        draw_minimap_bubble(descriptor) {
            
            // Render the minimap rectangle
            let mmm = descriptor.context.rect(
                this.CTX.minimap.bubble.lastMinimapBubbleX, 
                0, 
                this.CTX.minimap.bubble.lastMinimapBubbleW, 
                this.info.barHeight, {
                class: this.CTX.minimap.bubble.bubble_class,
                fill: "red",
                opacity: 0.5
            });

            // debugger; -> TODO Poglej kaj vrača mmm
        },

        viewportResized() {

            this.info.width = 2 * (window.innerWidth);
            this.info.barWidth = window.innerWidth;
            
            this._render_temp();
        },

        init(config) {

            if(config && config.cursor && config.cursor.enabled){
                this.info.cursor.enabled = config.cursor.enabled;
            }

            // VexFlow Magic
            let VF = Vex.Flow;
            
            //for(let ctx_key in this.CTX) // Every possible context
            this.enabledContexts.forEach((ctx_key) => {

                var div = document.getElementById(this.CTX[ctx_key].id)
                var renderer = new VF.Renderer(div, VF.Renderer.Backends.SVG);

                this.CTX[ctx_key].el = div;
                this.CTX[ctx_key].parentElement = div.parentElement;
                this.CTX[ctx_key].scrollElement = div.parentElement;
                this.CTX[ctx_key].renderer = renderer;
                this.CTX[ctx_key].context  = renderer.getContext();
                this.CTX[ctx_key].enabled = true;

                if(this.CTX[ctx_key].init){
                    this.CTX[ctx_key].init(this);
                }
            });

        }

    },
    mounted(){

        let vue = this;
        window.onresize = function(event) {
            vue.viewportResized();
        }

        window.onorientationchange = function(event) {
            vue.viewportResized();
        }

    },

}
</script>
