<template>

    <div class="rhythm-game__keyboard_wrap">
        
        <div class="rhythm-game__keyboard show-normal">

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
            
            
            <div class="row rhythm-game__keyboard-row row-3 musisync-row">
                
                <div v-if="!settingsVisible">
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
                        <sexy-button v-else customClass="musisync" color="green" @click.native="tie()" >
                            <span class="tie-text">u</span>
                        </sexy-button>

                    </div>

                    <!-- SELECTION -->
                    <selection-button :color="select_button_color" @click.native="selection()" />
                </div>

                <div v-if="settingsVisible" class="jcsb">
                    
                    <b-p-m-text-button 
                        v-for="i in [60, 70, 80, 90]"  :key="i" 
                        @click.native="setBPM(i, true)" :text="i" 
                        :selected="playbackStatus.BPM == i" />

                    <sexy-button :color="playbackStatus.metronome ? 'sunglow' : 'cabaret'" @click.native="playbackStatus.toggleMetronome(true)">
                        <div v-if="playbackStatus.metronome" class="normal-font">IZKLJUČI METRO<br>NOM</div>
                        <div v-else class="normal-font">VKLJUČI METRO<br>NOM</div>
                    </sexy-button>
                </div>

            </div>
            
            
            <div class="row rhythm-game__keyboard-row row-4">
                
                <!-- BACKSPACE BUTTON -->
                <sexy-button color="cabaret"   @click.native="delete_note()"  >
                    <icon name="trash" scale="2" />
                </sexy-button>
                
                <!-- PLAY EXERCISE BUTTON -->
                <play-button @click.native="play_exercise()" text="Ponovi vajo" :percents="percentsExercise" :playing="playbackStatus.playing" />

                <!-- BPM SLIDER / BUTTON -->
                <!--<b-p-m-slider :bpmObject="playbackStatus" valueKey="BPM" />-->
                <sexy-button v-if="!settingsVisible" color="sunglow" @click.native="show_settings()"><icon name="cog" scale="2" /></sexy-button>
                <sexy-button v-else color="cabaret" @click.native="hide_settings()"><icon name="cog" scale="2" /></sexy-button>
                
                <!-- CHECK button -->
                <check-button :color="checkButtonColor" :status="question.check" :percents="(1- question.statistics.duration / question.maxSeconds)*100" :textSecond="checkButtonTextSecond" :textThird="checkButtonTextThird" @click.native="check()"/>

                <!-- HELP BUTTON -->
                <sexy-button color="green" @click.native="showHelp()" ><div class="tiny-tajni-pici-mici-font">Pomoč</div></sexy-button>

            </div>

            <div class="row rhythm-game__keyboard-row row-5 show-normal">
                
                <!-- BACKSPACE BUTTON -->
                <sexy-button color="cabaret"   @click.native="feedback()"  >
                    <div class="tiny-tajni-pici-mici-font">Povratne informacije</div>
                </sexy-button>

                <sexy-button @click.native="toggleMenu()"  >
                    <div class="tiny-tajni-pici-mici-font">Odpri Zapri Meni</div>
                </sexy-button>

            </div>
            

        </div>

        <div class="rhythm-game__keyboard rhythm-game__keyboard-landscape-phone hide-normal">

            <div class="row norfolk-row">

                <div v-if="!settingsVisible" class="row" style="display: inline-block;">

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

                <div v-if="settingsVisible" style="display: inline-block" class="rhythm-game__keyboard-row">
                    
                    <b-p-m-text-button 
                        v-for="i in [60, 70, 80, 90]"  :key="i" 
                        @click.native="setBPM(i, true)" :text="i" 
                        :selected="playbackStatus.BPM == i" />

                    <sexy-button :color="playbackStatus.metronome ? 'sunglow' : 'cabaret'" @click.native="playbackStatus.toggleMetronome(true)">
                        <div v-if="playbackStatus.metronome" class="normal-font">IZKLJUČI METRO<br>NOM</div>
                        <div v-else class="normal-font">VKLJUČI METRO<br>NOM</div>
                    </sexy-button>
                
                </div>

                <sexy-button v-if="(moving_buttons || cursor.in_tuplet) && !settingsVisible" text="<" color="orange" @click.native="move_cursor_backwards" customClass="moveButtonsButton" />
                <bar-button v-if="!settingsVisible && !moving_buttons" :hidden="cursor.in_tuplet" color="green" @click.native="add_bar()" />
                
                <sexy-button v-if="(moving_buttons || cursor.in_tuplet) && !settingsVisible" text=">" color="orange" @click.native="move_cursor_forward" customClass="moveButtonsButton" />
                <dot-button v-if="!settingsVisible && !moving_buttons" :hidden="cursor.in_tuplet" @click.native="dot()" />
                <play-button v-if="settingsVisible" @click.native="play_exercise()" text="Ponovi vajo" :playing="playbackStatus.playing" />

                <!-- MOVE SWITCH -->
                <sexy-button v-if="moving_buttons && !settingsVisible" :hidden="cursor.in_tuplet" text="j \"  color="green" @click.native="moving_buttons = !moving_buttons" customClass="musisync" />
                <sexy-button v-if="!settingsVisible && !moving_buttons" :hidden="cursor.in_tuplet" text="< >"  color="orange" @click.native="moving_buttons = !moving_buttons" customClass="moveButtonsButton moveButtonsButton_Switch" />
                <sexy-button v-if="settingsVisible" color="green" @click.native="showHelp()" ><div class="tiny-tajni-pici-mici-font">Pomoč</div></sexy-button>

                <sexy-button v-if="settingsVisible" @click.native="toggleMenu()" customClass="normal-font" text="Odpri Zapri meni"/>

                <sexy-button v-if="!settingsVisible" color="sunglow" @click.native="show_settings()"><icon name="cog" scale="2" /></sexy-button>
                <sexy-button v-else color="cabaret" @click.native="hide_settings()"><icon name="cog" scale="2" /></sexy-button>
                

            </div>

            <div class="row norfolk-row">

                <sexy-button v-if="!cursor.selectionMode" :color="rest_color()" @click.native="rest(2)" >&#x00D3;</sexy-button>
                <sexy-button v-else :color="rest_color()" @click.native="remove_tuplets()" >
                    <span class="musisync">T</span>
                    <icon name="ban" scale="2" class="alert"></icon>
                </sexy-button>
                
                <sexy-button :color="rest_color()" @click.native="rest(4)" >&#x0152;</sexy-button>
                <sexy-button :color="rest_color()" @click.native="rest(8)" >&#x2030;</sexy-button>
                <sexy-button :color="rest_color()" @click.native="rest(16)">&#x2248;</sexy-button>
                <sexy-button :color="rest_color()" @click.native="rest(32)">&#x00AE;</sexy-button>
                    
                <sexy-button customClass="musisync overflow-hidden" color="green" @click.native="tie()" >
                    <span class="tie-text">u</span>
                </sexy-button>    

                
                <selection-button :color="select_button_color" @click.native="selection()" />
                
                
                <sexy-button color="cabaret"   @click.native="delete_note()" >
                    <icon name="trash" scale="2" />
                </sexy-button>


                <check-button :color="checkButtonColor" :status="question.check" :percents="(1- question.statistics.duration / question.maxSeconds)*100" :textSecond="checkButtonTextSecond" :textThird="checkButtonTextThird" @click.native="check()"/>

            </div>

        </div>
            
    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../../sass/variables/index';
    
    .half_transparent{
        opacity: 0.5;
    }

    .overflow-hidden {
        overflow: hidden;
    }

    .tie-text {
        font-size: 131px;
        margin-bottom: 115px;
        margin-left: 7px;
    }

    .rhythm-game__keyboard_wrap {
        padding: 0 10px 0 10px;
        display: flex;
        justify-content: center;

        @include comfortable-screen {
            padding-top: 70px;
        }
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

    .jcsb {
        display: flex;
        justify-content: space-between;
    }
    

    .moveButtonsButton {
        font-family: initial !important;
        font-size: 26px  !important;
        font-weight: bold;
    }

    .normal-font{
        font-family: $font-bold !important;
        font-size: 8pt !important;
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
import BPMTextButton from "./Buttons/BPMTextButton.vue"
import CheckButton from "./Buttons/CheckButton.vue"

import 'vue-awesome/icons/repeat'
import 'vue-awesome/icons/trash'
import 'vue-awesome/icons/play'
import 'vue-awesome/icons/stop'
import 'vue-awesome/icons/question-circle'
import 'vue-awesome/icons/user-o'
import 'vue-awesome/icons/check'
import 'vue-awesome/icons/angle-double-right'
import 'vue-awesome/icons/times'
import 'vue-awesome/icons/i-cursor'
import 'vue-awesome/icons/ban'
import 'vue-awesome/icons/refresh'
import 'vue-awesome/icons/exclamation-circle'
import 'vue-awesome/icons/cog'

var Fraction = require('fraction.js');

export default {
    
    methods: {

        note(num) {

            this.key_callback({
                type: 'n',
                value: num
            });

        },

        show_settings() {

            this.settingsVisible = true;
        },

        hide_settings() {

            this.settingsVisible = false;
        },

        toggleMenu() {

            this.key_callback({
                type: "toggleMenu"
            });
        },

        showJson() {

            this.key_callback({
                type: "showJson"
            });

        },

        setBPM(value, reload) {
            this.playbackStatus.setBPM(value, reload);
        },

        feedback() {

            this.key_callback({
                type: "feedback"
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

        play_button_click(type){

            if(!this.playbackStatus)
                return;

            // alert("RECEIVED! Status: "+this.playbackStatus.playing);

            if(this.playbackStatus.playing){
                this.key_callback({
                    type: 'playback',
                    action: 'stop',
                });
            } else {
                this.key_callback({
                    type: 'playback',
                    action: 'replay',
                    what: type
                });
            }

            
        },

        play_user(){
          this.play_button_click("user");  
        },

        play_exercise() {
            this.play_button_click("exercise");
        },

        show_diff() {
            this.key_callback({
                type: 'submit'
            });
        },

        check(){

            this.key_callback({
                type: 'check'
            })

        },

        submit(){

            this.key_callback({
                type: 'submit'
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
        }

    },
    components: {
        SexyButton, SexySlider, TupletSign, BPMTextButton,

        ThirtyTwoButton, BarButton, DotButton, SelectionButton, PlayButton, BPMSlider, CheckButton

    },
    data: function() {
        return {

            btn_idx: [0,1,2,3,4],
            available_notes: [2, 4, 8, 16, 32],
            available_tuplets: [],

            rest_mode: false,
            moving_buttons: false,

            moreButton: false,

            buttons: false,

            settingsVisible: false,

            bpm_slider: {
                value: 10
            },

            cursor: {}

        };
    },
    props: [
        'key_callback', 'playbackStatus', 'question', 'say',
    ],
    computed: {

        percentsUser(){
            
            if(this.playbackStatus && this.playbackStatus.currentlyLoaded == "user"){
                
                return this.playbackStatus.percentPlayed();

            } else{
                return 0;
            }
        },

        percentsExercise(){
            if(this.playbackStatus && this.playbackStatus.currentlyLoaded == "exercise"){
                
                return this.playbackStatus.percentPlayed();

            } else{
                return 0;
            }
        },

        checkButtonTextSecond() {

            // let seconds = this.question.maxSeconds - this.question.statistics.duration;
            // return seconds+"s ";
            return "";
        },

        checkButtonTextThird() {
            //return this.question.statistics.nChecks+"/"+this.question.maxChecks
            return "";
        },

        checkButtonColor(){
            if(this.question.check == "no") return "cabaret";
            else if(this.question.check == "wrong") return "red";
            else if(this.question.check == "correct") return "green";
            else if(this.question.check == "next") return "sunglow";
            return "cabaret";
        },

        checkButtonIcon(){
            if(this.question.check == "no") return "question-circle";
            else if(this.question.check == "wrong") return "times";
            else if(this.question.check == "correct") return "check";
            else if(this.question.check == "next") return "angle-double-right";
            return "question-circle";
        },

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
