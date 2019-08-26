<template>

    <div class="rhythm-game__keyboard_wrap">
        
        <div class="rhythm-game__keyboard">


            <div class="row rhythm-game__keyboard-row row-1 norfolk-row" >

                <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(2)" ><div class="norfolk-note-padding">&#x0068;</div></sexy-button>
                <sexy-button v-else :color="note_color()" @click.native="tuplet(3,2)"><TupletSign num="3" :bg="note_color()" /></sexy-button>
                
                <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(4)" ><div class="norfolk-note-padding">&#x0071;</div></sexy-button>
                <sexy-button v-else :color="note_color()" @click.native="tuplet(2,3)"><TupletSign num="2" :bg="note_color()" /></sexy-button>
                
                <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(8)" ><div class="norfolk-note-padding">&#x0065;</div></sexy-button>
                <sexy-button v-else :color="note_color()" @click.native="tuplet(5,4)"><TupletSign num="5:4" :bg="note_color()" /></sexy-button>
                
                <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(16)"><div class="norfolk-note-padding">&#x0078;</div></sexy-button>
                <sexy-button v-else :color="note_color()" @click.native="tuplet(6,4)"><TupletSign num="6:4" :bg="note_color()" /></sexy-button>

                <ThirtyTwoButton v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(32)" />
                <sexy-button v-else :color="note_color()" @click.native="tuplet()"><TupletSign num="?:?" :bg="note_color()" /></sexy-button>

            </div>
            
            
            <div class="row rhythm-game__keyboard-row row-2 norfolk-row">
                
                <sexy-button v-if="!cursor.selectionMode" :color="rest_color()" @click.native="rest(2)" >&#x00D3;</sexy-button>
                <sexy-button v-else :color="rest_color()" @click.native="remove_tuplets()" >
                    <span class="musisync">T</span>
                    <icon name="ban" scale="2" class="alert"></icon>
                </sexy-button>
                
                <div v-bind:class="{ half_transparent: cursor.selectionMode }" style="display: inline-block;">
                
                    <sexy-button :color="rest_color()" @click.native="rest(4)" >&#x0152;</sexy-button>
                    <sexy-button :color="rest_color()" @click.native="rest(8)" >&#x2030;</sexy-button>
                    <sexy-button :color="rest_color()" @click.native="rest(16)">&#x2248;</sexy-button>
                    <sexy-button :color="rest_color()" @click.native="rest(32)">&#x00AE;</sexy-button>
                
                </div>

            </div>
            
            
            <div class="row rhythm-game__keyboard-row row-3 musisync-row show-normal">
                
                <div v-bind:class="{ half_transparent: cursor.selectionMode }" style="display: inline-block;">

                    <BarButton :hidden="cursor.in_tuplet" @click.native="add_bar()" />
                
                    <!-- MOVE LEFT OR DOT -->
                    <sexy-button v-if="moving_buttons || cursor.in_tuplet" text="<" color="orange" @click.native="move_cursor_backwards" customClass="moveButtonsButton" />
                    <dot-button v-else :hidden="cursor.in_tuplet" @click.native="dot()" />

                    <!-- MOVE SWITCH -->
                    <sexy-button v-if="moving_buttons" :hidden="cursor.in_tuplet" text=". u"  color="green" @click.native="moving_buttons = !moving_buttons" />
                    <sexy-button v-else :hidden="cursor.in_tuplet" text="< >"  color="orange" @click.native="moving_buttons = !moving_buttons" customClass="moveButtonsButton" />
                    
                    <!-- MOVE RIGHT OR TIE -->
                    <sexy-button v-if="moving_buttons || cursor.in_tuplet" text=">" color="orange" @click.native="move_cursor_forward" customClass="moveButtonsButton" />
                    <sexy-button v-else text="u" color="green" @click.native="tie()" />

                </div>

                <selection-button :color="select_button_color" @click.native="selection()" />

            </div>
            
            
            <div class="row rhythm-game__keyboard-row row-4 show-normal">
                
                <!-- BACKSPACE BUTTON -->
                <sexy-button color="cabaret"   @click.native="delete_note()"  >
                    <icon name="trash" scale="2" />
                </sexy-button>
                
                <!-- DEBUG -->
                <sexy-button color="sunglow" :cols="1" @click.native="changeDifficulty()" >
                    <div class="tiny-tajni-pici-mici-font">Nastavi težavnost</div>
                </sexy-button>

                <sexy-button color="sunglow" :cols="1" @click.native="changeSignature()" >
                    <div class="tiny-tajni-pici-mici-font">Spremeni<br> taktovski<br> način</div>
                </sexy-button>

                <sexy-button color="green" :cols="1"  @click.native="saveBar()"  >
                    <div v-if="buttonState.save == 'normal'" class="tiny-tajni-pici-mici-font">Shrani</div>
                    <icon name="check" v-if="buttonState.save == 'ok'"/>
                    <icon name="refresh" v-if="buttonState.save == 'loading'" spin />
                    <icon name="exclamation-circle" v-if="buttonState.save == 'error'" />
                </sexy-button>
                
                <sexy-button color="cabaret" v-if="selected.id" :cols="1"  @click.native="deleteBar()"  >
                    <div v-if="buttonState.deleteBar == 'normal'" class="tiny-tajni-pici-mici-font">Izbriši takt</div>
                    <icon name="check" v-if="buttonState.deleteBar == 'ok'"/>
                    <icon name="refresh" v-if="buttonState.deleteBar == 'loading'" spin />
                    <icon name="exclamation-circle" v-if="buttonState.deleteBar == 'error'" />
                </sexy-button>

            </div>

            <div class="row rhythm-game__keyboard-row row-5 show-normal">

                

            </div>

            

        </div>
            
    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../../sass/variables/index';
    
    .half_transparent{
        opacity: 0.5;
    }

    .rhythm-game__keyboard_wrap {
        padding: 0 10px 0 10px;
        display: flex;
        justify-content: center;
    }

    .rhythm-game__keyboard {
        font-size: 20px;
        touch-action: manipulation;
    }

    .rhythm-game__keyboard-row{
        margin-bottom: 5px;
    }

    .rhythm-game__keyboard-row .button{
        margin-left: 4px;
        font-size: 40px;
    }

    .norfolk-row .button  {
        font-family: Norfolk;
        font-size: 33px;
    }

    .norfolk {
        font-family: Norfolk !important;
    }

    .musisync-row .button {
        font-family: MusiSync;
        font-size: 33px;
    }

    .musisync {
        font-family: MusiSync !important;
    }

    

    .moveButtonsButton {
        font-family: initial !important;
        font-size: 26px  !important;
        font-weight: bold;
    }

    .normal-font{
        font-family: inherit !important;
    }

    .tiny-tajni-pici-mici-font{
        font-size: 8pt !important;
        font-family: $font-bold;
    }

    .norfolk-note-padding{
        padding-top: 19px;
    }




    //
    // RESPONSIVE CLASSES
    //
    .hide-normal{
        display:none;
    }

    // For 
    @include breakpoint-small-phone-landscape {

        .hide-normal{
            display: inline-block;
        }

        .show-normal{
          display: none;
        }

        .rhythm-game__keyboard-row{
            margin-bottom: 0px;
        }
    }

    @include breakpoint-phone-landscape {
        .hide-normal{
            display: inline-block;
        }

        .show-normal{
          display: none;
        }

        .rhythm-game__keyboard-row{
            margin-bottom: 0px;
        }

        .button_1_col{
            width: 50px !important;
            height: 50px !important;
        }

    }


    // For iPhone 6,7,8 screens
    @include breakpoint-phone-portrait{

        .button_1_col{
            width: 60px !important;
            height: 60px !important;
        }

    }

</style>


<script>

import SexyButton from "../../../elements/SexyButton.vue"
import SexySlider from "../../../elements/SexySlider.vue"

import TupletSign from "../../../elements/TupletSign.vue"
import ThirtyTwoButton from "./Buttons/ThirtyTwoButton.vue"
import BarButton from "./Buttons/BarButton.vue"
import DotButton from "./Buttons/DotButton.vue"
import SelectionButton from "./Buttons/SelectionButton.vue"
import PlayButton from "./Buttons/PlayButton.vue"
import BPMSlider from "./Buttons/BPMSlider.vue"
import CheckButton from "./Buttons/CheckButton.vue"


import 'vue-awesome/icons/trash'
import 'vue-awesome/icons/repeat'
import 'vue-awesome/icons/play'
import 'vue-awesome/icons/question-circle'
import 'vue-awesome/icons/user-o'
import 'vue-awesome/icons/pause'
import 'vue-awesome/icons/check'
import 'vue-awesome/icons/angle-double-right'
import 'vue-awesome/icons/times'
import 'vue-awesome/icons/i-cursor'
import 'vue-awesome/icons/ban'
import 'vue-awesome/icons/refresh'
import 'vue-awesome/icons/exclamation-circle'

var Fraction = require('fraction.js');

export default {
    
    methods: {

        note(num) {

            this.key_callback({
                type: 'n',
                value: num
            });

        },

        changeDifficulty(num) {

            this.key_callback({
                type: 'changeDifficulty'
            });

        },

        showJson() {

            this.key_callback({
                type: "showJson"
            });

        },

        showHelp() {
            this.key_callback({
                type: "showHelp"
            });
        },

        changeSignature() {
            this.key_callback({
                type: "changeSignature"
            });
        },

        rest(num) {

            this.key_callback({
                type: 'r',
                value: num
            });

        },

        setCorrect(){
            
            this.key_callback({
                type: 'pass'
            });
            
        },

        add_bar(){

            this.key_callback({
                type: 'bar',
                value: 4
            });

        },


        move_cursor_forward(){

            this.key_callback({
                type: '>'
            });

        },

        move_cursor_backwards(){

            this.key_callback({
                type: '<'
            });

        },

        dot() {

            this.key_callback({
                type: 'dot'
            });

        },

        tie() {

            this.key_callback({
                type: 'tie'
            });

        },

        delete_note(){

            this.key_callback({
                type: 'delete'
            });

        },

        tuplet(num_notes, in_space_of) {

            let obj = null;
            if(parseInt(num_notes) && parseInt(in_space_of)){
                obj = {
                    num_notes: num_notes,
                    in_space_of: in_space_of
                }
            }

            this.key_callback({
                type: 'tuplet',
                tuplet_type: obj
            });

        },

        remove_tuplets(){

            this.key_callback({
                type: 'remove_tuplets'
            })

        },

        selection(){

            this.key_callback({
                type: "selectionMode"
            })

        },

        note_color() {
            return "green";
        },

        rest_color() {
            return "red";
        },

        hideNoteButtonWhenInTuplet(button_type){
            if(this.in_tuplet){
                return true;
            }
            return false;
        },

        init(cursor) {
            this.$set(this, 'cursor', cursor);
        },


        saveBar(){

            this.key_callback({
                type: 'saveBar'
            });

        },

        deleteBar(){

            this.key_callback({
                type: 'deleteBar'
            });

        },



    },
    components: {
        SexyButton, SexySlider, TupletSign, 

        ThirtyTwoButton, BarButton, DotButton, SelectionButton, PlayButton, BPMSlider, CheckButton

    },
    data: function() {
        return {
            rest_mode: false,
            moving_buttons: false,

            moreButton: false,

            buttons: false,

            cursor: {},
        };
    },
    props: [
        'key_callback', 'buttonState', 'selected'
    ],
    computed: {

        tuplet_color() {
            if(this.cursor.editing_tuplet) return "sunglow";
            return "green";
        },

        select_button_color() {
            if(this.cursor.selectionMode) return "blue";
            return "green";
        }

    }

}
</script>
