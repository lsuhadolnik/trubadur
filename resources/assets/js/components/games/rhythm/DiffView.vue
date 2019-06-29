<template>

    <div class="rhythm-game__diff">
        
        <h1 class="diff-prompt">Taka je bila vaja:</h1>
        <div class="rhythm-game__diff__first-row">
            <div id="diff-first-row" ></div>
        </div>
        
        <h1 class="diff-prompt">Tole je bilo vpisano:</h1>
        <div class="rhythm-game__diff__second-row">
            <div id="diff-second-row"></div>
        </div>
        
        <div class="button-holder">
            <SexyButton color="green" @click.native="dismiss()" :cols="2" >Vredu</SexyButton>
        </div>

    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';


    .rhythm-game__diff__first-row, .rhythm-game__diff__second-row  {
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
        overflow-scrolling: touch;
        height: 163px;
        overflow-y: hidden;

        @include breakpoint-phone-landscape { height: 150px; }
    }

    #diff-first-row, #diff-second-row {
        -webkit-transform: scale(2) translate(25%, 25%);
        transform: scale(2) translate(25%, 25%);
    }

    .diff-prompt{
        padding: 0px;
        margin: 10px 0 0 0;
        font-size: 26px;
        text-align: center;
    }

    .button-holder{
        display: flex;
        width: 100%;
        justify-content: center;
    }


</style>


<script>

import Vex from 'vexflow'
import RhythmRenderUtilities from './rhythmRenderUtilities'

import SexyButton from "../../elements/SexyButton.vue"

var Fraction = require('fraction.js');

var VF = Vex.Flow;

export default {

    props: [
        'dismiss', 
    ],

    components: {SexyButton},

    data () {
        return {

            info: {
                width: 2*(window.innerWidth),
                height: 75,
                barWidth: window.innerWidth,
                barHeight: 75,
                barOffsetY: 16,

                scrollBuffer: {
                    minimapX: 0,
                    scrollX: 0
                },

            },

            CTX: {
                exercise: {
                    id: "diff-first-row",
                    role: "exercise",
                }, 
                user: {
                    id: "diff-second-row",
                    role: "user",
                }
            }
        }
    },

    methods: {

        scrolled: function(x, hasScrolled){

            for (let key in this.CTX){
                let view = document.getElementById(this.CTX[key].id).parentElement;
                view.scroll(x,0);
            }

        },

       _render_context(descriptor, notes, bar){



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
                let symbol = thisNote.value + "";;
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

            if(renderQueue.length > 0){
                // Draw the rest
                batches.push({notes:renderQueue, width:currentBatchWidth});
            }

            // Finally render everything
            RU._vex_render_batches(context, batches, [ties, tuplets], {
                bar: bar,
                barOffsetY: this.info.barOffsetY,
                width: this.info.width
            });

            // set CTX.zoomview.x_coords property
            this.retrieveXCoords(descriptor);


        },


        render(exerciseNotes, userNotes, bar) {
            
            this._render_context(
                this.CTX.user, 
                userNotes, 
                bar
            );

            this._render_context(
                this.CTX.exercise,
                exerciseNotes,
                bar
            );

        },

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
        
        var fR = this.CTX.exercise.parentElement;
        var sR = this.CTX.user.parentElement;
        
        var vue = this;
        fR.onscroll = function(e){
            vue.scrolled(fR.scrollLeft, true);
            //e.preventDefault();
            return false;
        }

        sR.onscroll = function(e){
            vue.scrolled(sR.scrollLeft, true);
            //e.preventDefault();
            return false;
        }

        /*window.onresize = function(event) {
            vue.viewportResized();
        }

        window.onorientationchange = function(event) {
            vue.viewportResized();
        }*/

        

    },

}
</script>
