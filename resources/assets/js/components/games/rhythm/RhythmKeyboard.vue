<template>

    <div class="rythm-game__keyboard_wrap">
        
        <div class="rhythm-game__keyboard">

            <div class="row rhythm-game__keyboard-row row-1 norfolk-row" >

                <!--<div v-bind:class="{ half_transparent: cursor.selectionMode }">-->

                    <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(2)" ><div class="norfolk-note-padding">&#x0068;</div></sexy-button>
                    <sexy-button v-else :color="note_color()" @click.native="tuplet(3)"><TupletSign num="3" :bg="note_color()" /></sexy-button>
                    
                    <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(4)" ><div class="norfolk-note-padding">&#x0071;</div></sexy-button>
                    <sexy-button v-else :color="note_color()" @click.native="tuplet(5)"><TupletSign num="5" :bg="note_color()" /></sexy-button>
                    
                    <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(8)" ><div class="norfolk-note-padding">&#x0065;</div></sexy-button>
                    <sexy-button v-else :color="note_color()" @click.native="tuplet(6)"><TupletSign num="6" :bg="note_color()" /></sexy-button>
                    
                    <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(16)"><div class="norfolk-note-padding">&#x0078;</div></sexy-button>
                    <sexy-button v-else :color="note_color()" @click.native="tuplet(9)"><TupletSign num="9" :bg="note_color()" /></sexy-button>

                    <sexy-button v-if="!cursor.selectionMode" :color="note_color()" @click.native="note(32)">
                        <div class="norfolk-note-padding thirtytwo-note">
                            <div class="td-base"> &#x0071;</div>
                            <div class="td-flag1">&#x006A;</div>
                            <div class="td-flag2">&#x006A;</div>
                            <div class="td-flag3">&#x006A;</div>
                        </div>
                    </sexy-button>
                    <sexy-button v-else :color="note_color()" @click.native="tuplet()"><TupletSign num="?" :bg="note_color()" /></sexy-button>

                <!--</div>-->
                
                <!-- RESPONSIVE DIRECTIVES -->
                <!-- Will show only on small wide screens (landscape phones) -->
                <div class="hide-normal">
                    
                    <sexy-button :hidden="cursor.in_tuplet" text="\" color="green" @click.native="add_bar()" />                    
                    <sexy-button :hidden="cursor.in_tuplet" text="." color="green" @click.native="dot()" />

                    <sexy-button color="sunglow" @click.native="play_exercise()" :percents="percentsExercise" customClass="normal-font tiny-tajni-pici-mici-font">
                        <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'exercise'"/>
                        <div v-else class="small-font-button">Ponovi vajo</div>
                    </sexy-button>

                    <sexy-button color="sunglow" @click.native="play_user()" :percents="percentsUser" customClass="normal-font tiny-tajni-pici-mici-font">
                        <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'user'"/>
                        <div v-else class="small-font-button">Ponovi vpisano</div>
                    </sexy-button>

                </div>

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

                <!-- RESPONSIVE DIRECTIVES -->
                <!-- Will show only on small wide screens (landscape phones) -->
                <div class="hide-normal">
                    
                    <sexy-button text="u"  color="green" @click.native="tie()" />    
                    <sexy-button text="T" :color="tuplet_color" @click.native="tuplet()" />
                    <sexy-button color="cabaret"   @click.native="delete_note()" >
                        <img src="/images/backspace.svg" width="30" />
                    </sexy-button>
                    <sexy-button :color="checkButtonColor" @click.native="check()" customClass="normal-font tiny-tajni-pici-mici-font">
                        <div class="small-font-button">Preveri</div>
                    </sexy-button>

                </div>

            </div>
            <div class="row rhythm-game__keyboard-row row-3 musisync-row show-normal">
                
                <div v-bind:class="{ half_transparent: cursor.selectionMode }" style="display: inline-block;">

                    <sexy-button :hidden="cursor.in_tuplet" text="\" color="green" @click.native="add_bar()" />
                
                    <!-- MOVE LEFT OR DOT -->
                    <sexy-button v-if="moving_buttons || cursor.in_tuplet" text="<" color="orange" @click.native="move_cursor_backwards" customClass="moveButtonsButton" />
                    <sexy-button v-else :hidden="cursor.in_tuplet" text="." color="green" @click.native="dot()" />
                    
                    <!-- MOVE SWITCH -->
                    <sexy-button v-if="moving_buttons" :hidden="cursor.in_tuplet" text=". u"  color="green" @click.native="moving_buttons = !moving_buttons" />
                    <sexy-button v-else :hidden="cursor.in_tuplet" text="< >"  color="orange" @click.native="moving_buttons = !moving_buttons" customClass="moveButtonsButton" />
                    
                    <!-- MOVE RIGHT OR TIE -->
                    <sexy-button v-if="moving_buttons || cursor.in_tuplet" text=">" color="orange" @click.native="move_cursor_forward" customClass="moveButtonsButton" />
                    <sexy-button v-else text="u" color="green" @click.native="tie()" />

                </div>

                <!-- SELECTION -->
                <sexy-button :color="select_button_color" @click.native="selection()" >
                    <icon name="i-cursor" />
                </sexy-button>
                <!--<div v-if="cursor.selection">
                Base: {{cursor.selection.base}}
                From: {{cursor.selection.from}}
                To: {{cursor.selection.to}}
                </div>-->

            </div>
            <div class="row rhythm-game__keyboard-row row-4 show-normal">
                
                <!-- BACKSPACE BUTTON -->
                <sexy-button color="cabaret"   @click.native="delete_note()"  >
                    <img src="/images/backspace.svg" width="30" />
                </sexy-button>
                
                <!-- PLAY EXERCISE BUTTON -->
                <sexy-button color="sunglow" @click.native="play_exercise()" :percents="percentsExercise">
                    <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'exercise'"/>
                    <div v-else class="tiny-tajni-pici-mici-font">Ponovi vajo</div>
                </sexy-button>

                <!-- BPM SLIDER / BUTTON -->
                <sexy-slider color="cabaret" :value="playbackStatus" valueKey="BPM" :from="20" :to="250">
                    <div class="BPM-indicator normal-font">
                        <div class="BPM-value">{{playbackStatus.BPM}}</div>
                        <div class="BPM-prompt">BPM</div>
                    </div>
                </sexy-slider>
                
                <!-- CHECK button -->
                <sexy-button :color="checkButtonColor" @click.native="check()" >
                    <div v-if="question.check == 'no'" class="tiny-tajni-pici-mici-font">Preveri</div>
                    <icon name="times" v-if="question.check == 'wrong'"/>
                    <icon name="check" v-if="question.check == 'correct'"/>
                    <div v-if="question.check == 'next'" class="tiny-tajni-pici-mici-font">Naprej</div>
                </sexy-button>

                <!-- SUBMIT BUTTON -->
                <sexy-button :color="checkButtonColor" @click.native="submit()" >
                    <div class="tiny-tajni-pici-mici-font">Oddaj</div>
                </sexy-button>

                <sexy-button :color="checkButtonColor" @click.native="showJson()" >
                    <div class="tiny-tajni-pici-mici-font">Show JSON</div>
                </sexy-button>

                <sexy-button :color="checkButtonColor" @click.native="changeSignature()" >
                    <div class="tiny-tajni-pici-mici-font">Change Signature</div>
                </sexy-button>

                <sexy-button color="sunglow" @click.native="play_user()" :percents="percentsUser" customClass="normal-font tiny-tajni-pici-mici-font">
                        <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'user'"/>
                        <div v-else class="small-font-button">Ponovi vpisano</div>
                </sexy-button>
                
                <!-- SET CORRECT BUTTON -->
                <!--<sexy-button :color="checkButtonColor" @click.native="setCorrect()" >
                    <div class="tiny-tajni-pici-mici-font">Set correct</div>
                </sexy-button>-->

            </div>

        </div>
            
    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';
    
    .half_transparent{
        opacity: 0.5;
    }

    .rythm-game__keyboard_wrap {
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

    .norfolk-row .button, .norfolk {
        font-family: Norfolk;
        font-size: 33px;
    }

    .musisync-row .button, .musisync {
        font-family: MusiSync;
        font-size: 33px;
    }

    .BPM-indicator{
        font-size: 16px;
    }
    .BPM-slider {
        width: 80px;
    }

    .thirtytwo-note{

        .td-flag1, .td-flag2, .td-flag3{
            font-size: 30px;
            margin-left: 17px;
        }

        .td-base{
            margin-top: -16px;
        }

        .td-flag1{
            margin-top: -89px;
        }

        .td-flag2{
            margin-top: -53px;
        }
        
        .td-flag3{
            margin-top: -53px;
        }

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
    }


    // For iPhone 6,7,8 screens
    @include breakpoint-phone{

        .button_1_col{
            width: 60px !important;
            height: 60px !important;
        }

    }

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"
import TupletSign from "../../elements/TupletSign.vue"
import SexySlider from "../../elements/SexySlider.vue"
import TwoRowsButton from "../../elements/TwoRowsButton.vue"

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

var Fraction = require('fraction.js');

export default {
    
    methods: {

        note(num) {

            this.key_callback({
                type: 'n',
                value: num
            });

        },

        showJson() {

            this.key_callback({
                type: "showJson"
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

        tuplet(num) {

            this.key_callback({
                type: 'tuplet',
                tuplet_type: num
            });

        },

        remove_tuplets(){

            this.key_callback({
                type: 'remove_tuplets'
            })

        },

        play_button_click(type){

            if(this.playbackStatus.currentlyLoaded == type){
    
                if(this.playbackStatus.playing){
                
                    this.key_callback({
                        type: 'playback',
                        action: 'pause',
                    });
                }
                else{
                    this.key_callback({
                        type: 'playback',
                        action: 'resume',
                    });
                }

            }
            else {

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

        pause() {

            this.key_callback({
                type: 'playback',
                action: 'pause',
            });

        },

        resume() {

            this.key_callback({
                type: 'playback',
                action: 'resume'
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

    },
    components: {
        SexyButton, TwoRowsButton, SexySlider, TupletSign
    },
    data: function() {
        return {
            playback_throttle: 2,
            rest_mode: false,
            moving_buttons: false,

            buttons: false,

            bpm_slider: {
                value: 10
            },

        };
    },
    props: [
        'key_callback', 'cursor', 'playbackStatus', 'question', 'say',
    ],
    computed: {

        percentsUser(){
            if(this.playbackStatus.currentlyLoaded == "user"){
                
                return this.playbackStatus.percentPlayed();

            } else{
                return 0;
            }
        },

        percentsExercise(){
            if(this.playbackStatus.currentlyLoaded == "exercise"){
                
                return this.playbackStatus.percentPlayed();

            } else{
                return 0;
            }
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
