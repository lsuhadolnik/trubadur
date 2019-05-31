<template>

    <div class="rythm-game__keyboard_wrap">
        
        <div class="rhythm-game__keyboard">

            <div class="row rhythm-game__keyboard-row " >
                <!--<sexy-button color="orange" @click.native="move_cursor_forward()" >
                    <span style="font-family: Gotham round;">
                        &gt;
                    </span>
                </sexy-button>-->

                <sexy-button :color="note_color()" @click.native="note(2)" >
                    <div class="norfolk-note-button-text">
                        &#x0068;
                    </div>
                </sexy-button>

                <sexy-button :color="note_color()" @click.native="note(4)" >
                    <div class="norfolk-note-button-text">
                        &#x0071;
                    </div>
                </sexy-button>

                <sexy-button :color="note_color()" @click.native="note(8)" >
                    <div class="norfolk-note-button-text">
                        &#x0065;
                    </div>
                </sexy-button>

                <sexy-button :color="note_color()" @click.native="note(16)"  >
                    <div class="norfolk-note-button-text">
                        &#x0078;
                    </div>
                </sexy-button>

                <sexy-button :color="note_color()" @click.native="note(32)"  >
                    <div class="norfolk-note-button-text thirtytwo-note">
                        <div class="td-base"> &#x0071;</div>
                        <div class="td-flag1">&#x006A;</div>
                        <div class="td-flag2">&#x006A;</div>
                        <div class="td-flag3">&#x006A;</div>
                    </div>
                </sexy-button>
                
                
                <!-- Will show only on small wide screens (landscape phones) -->
                <div class="hide-normal">
                    <sexy-button :hidden="cursor.in_tuplet" text="\" color="green" @click.native="add_bar()" />
                    
                    <sexy-button :hidden="cursor.in_tuplet" text="." color="green" @click.native="dot()" />
                    
                    <sexy-button color="sunglow" @click.native="play_exercise()" :percents="percentsExercise" customClass="normal-font tiny-tajni-pici-mici-font">
                        <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'exercise'"/>
                        <div v-else class="small-font-button">Ponovi vajo</div>
                    </sexy-button>

                    <!--<sexy-button color="cabaret" customClass="wideButton normal-font" :cols="1" v-on:touchstart="touchStarted()" v-on:touchend="touchEnded()">
                        <div class="BPM-indicator normal-font">
                            <div class="BPM-value">{{playbackStatus.BPM}}</div>
                            <div class="BPM-prompt">BPM</div>
                        </div>
                    </sexy-button>-->
                    <sexy-button color="sunglow" @click.native="play_user()" :percents="percentsUser" customClass="normal-font tiny-tajni-pici-mici-font">
                        <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'user'"/>
                        <div v-else class="small-font-button">Ponovi vpisano</div>
                    </sexy-button>

                    
                    <!--<sexy-button color="sunglow" @click.native="play_user()" :percents="percentsUser" customClass="normal-font tiny-tajni-pici-mici-font">
                        <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'user'"/>
                        <div v-else class="small-font-button">Zaigraj vpisano</div>
                    </sexy-button>-->
                </div>


            </div>
            <div class="row rhythm-game__keyboard-row">
                <!--<sexy-button color="orange" @click.native="move_cursor_backwards()" >
                    <span style="font-family: Gotham round;">
                        &lt;
                    </span>
                </sexy-button>-->
                
                <sexy-button :color="rest_color()" @click.native="rest(2)"  >
                    <div class="norfolk">
                        &#x00D3;
                    </div>
                </sexy-button>
                
                <sexy-button :color="rest_color()" @click.native="rest(4)" >
                    <div class="norfolk">
                        &#x0152;
                    </div>
                </sexy-button>
                
                <sexy-button :color="rest_color()" @click.native="rest(8)">
                    <div class="norfolk">
                        &#x2030;
                    </div>
                </sexy-button>

                <sexy-button :color="rest_color()" @click.native="rest(16)">
                    <div class="norfolk">
                        &#x2248;
                    </div>
                </sexy-button>

                <sexy-button :color="rest_color()" @click.native="rest(32)" >
                    <div class="norfolk">
                        &#x00AE;
                    </div>
                </sexy-button>
                

                <!--<sexy-button :hidden="cursor.in_tuplet" :text="rest_mode_button_text" :color="rest_mode_button_color" @click.native="toggle_rest_mode()" />
                
                
                

                <!-- Will show only on small wide screens (landscape phones) -->
                <div class="hide-normal">
                    
                    <sexy-button text="u" color="green" @click.native="tie()" />
                    
                    <sexy-button text="T" color="green" @click.native="tuplet()" />
                    
                    <sexy-button color="cabaret"   @click.native="delete_note()" >
                        <img src="/images/backspace.svg" width="30" />
                    </sexy-button>
                    
                    <sexy-button :color="checkButtonColor" @click.native="check()" customClass="normal-font tiny-tajni-pici-mici-font">
                        <!--<icon :name="checkButtonIcon" />-->
                        <div class="small-font-button">Preveri</div>
                    </sexy-button>

                </div>




            </div>
            <div class="row rhythm-game__keyboard-row rhythm-game__keyboard-row_fourth show-normal">
                


                <!--<sexy-button color="sunglow" @click.native="play_exercise()" :percents="percentsExercise">
                    <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'exercise'"/>
                    <div v-else class="small-font-button">Ponovi vajo</div>
                </sexy-button>
                
                <sexy-button color="sunglow" @click.native="play_user()" :percents="percentsUser">
                    <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'user'"/>
                    <div v-else class="small-font-button">Zaigraj vpisano</div>
                </sexy-button>-->

                <sexy-button :hidden="cursor.in_tuplet" text="\" color="green" @click.native="add_bar()" />
                
                
                <sexy-button v-if="moving_buttons" text="<" color="orange" @click.native="move_cursor_backwards" customClass="moveButtonsButton" />
                <sexy-button v-else :hidden="cursor.in_tuplet" text="." color="green" @click.native="dot()" />
                
                <sexy-button v-if="moving_buttons" :hidden="cursor.in_tuplet" text=". u"  color="green" @click.native="moving_buttons = !moving_buttons" />
                <sexy-button v-else :hidden="cursor.in_tuplet" text="< >"  color="orange" @click.native="moving_buttons = !moving_buttons" customClass="moveButtonsButton" />
                
                <sexy-button v-if="moving_buttons" text=">" color="orange" @click.native="move_cursor_forward" customClass="moveButtonsButton" />
                <sexy-button v-else text="u" color="green" @click.native="tie()" />

                <sexy-button text="T" color="green" @click.native="tuplet()" />

            </div>


            <div class="row rhythm-game__keyboard-row rhythm-game__keyboard-row_third show-normal">
                <sexy-button color="cabaret"   @click.native="delete_note()"  >
                    <img src="/images/backspace.svg" width="30" />
                </sexy-button>
                
                <sexy-button color="sunglow" @click.native="play_exercise()" :percents="percentsExercise">
                    <icon name="pause" v-if="playbackStatus.playing && playbackStatus.currentlyLoaded == 'exercise'"/>
                    <div v-else class="tiny-tajni-pici-mici-font">Ponovi vajo</div>
                </sexy-button>

                <sexy-slider color="cabaret" :value="playbackStatus" valueKey="BPM" :from="20" :to="250">
                    <div class="BPM-indicator normal-font">
                        <div class="BPM-value">{{playbackStatus.BPM}}</div>
                        <div class="BPM-prompt">BPM</div>
                    </div>
                </sexy-slider>
                
                <!-- check button -->
                <sexy-button :color="checkButtonColor" @click.native="check()" >
                    <div v-if="question.check == 'no'" class="tiny-tajni-pici-mici-font">Preveri</div>
                    <icon name="times" v-if="question.check == 'wrong'"/>
                    <icon name="check" v-if="question.check == 'correct'"/>
                    <div v-if="question.check == 'next'" class="tiny-tajni-pici-mici-font">Naprej</div>
                </sexy-button>

                <sexy-button :color="checkButtonColor" @click.native="submit()" >
                    <div class="tiny-tajni-pici-mici-font">Naprej</div>
                </sexy-button>

            </div>
            <div class="row rhythm-game__keyboard-row rhythm-game__keyboard-row_third show-normal">

                <!--<sexy-button color="cabaret" customClass="wideButton normal-font" :cols="2">
                    <input class="BPM-slider" type="range" :min="playbackStatus.BPM_from" :max="playbackStatus.BPM_to" step="10" v-model="playbackStatus.BPM">
                </sexy-button>-->

            </div>
           <!--<div class="row rhythm-game__keyboard-row rhythm-game__keyboard-row_third show-normal">

                

            </div>-->

        </div>

        <!--<div class="row rhythm-game__control-keys">

            <sexy-button text="BRIŠI" color="green" w="175px" @click.native="delete_note()"/>
            <sexy-button text="PONOVI" color="green" w="175px" @click.native="repeat_exercise()" />
            <sexy-button text="PREVERI" color="orange" w="175px" @click.native="check()" />
                
        </div>-->
            
    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';

    .hide-normal{
        display:none;
    }

    .white-text{
        color: white;
    }

    .down-a-bit{
        padding-top: 10px;
    }

    .clearfix {
        clear: both;
    }

    .button-spacer {
        display: inline-block;
        width: 1px;
    }

    .normal-font{
        font-family: inherit !important;
    }

    .tiny-tajni-pici-mici-font{
        font-size: 8pt !important;
    }
    
    .rythm-game__keyboard_wrap {
        
        padding: 0 10px 0 10px;
        display: flex;
        justify-content: center;
    }

    .rhythm-game__keyboard {
        font-size: 20px;
        //margin-bottom: 20px;
        touch-action: manipulation;
    }

    .rhythm-game__keyboard-row{
        
        margin-bottom: 5px;
    }

    .rhythm-game__keyboard-row .button{
        //width: 50px;
        //font-family: Norfolk;
        font-family: MusiSync;
        margin-left: 4px;
        font-size: 40px;
    }

    .rhythm-game__event-keys {
        display: flex;
        justify-content: center;
    }

    .rhythm-game__control-keys{
        display: flex;
        justify-content: center;
    }

    .rhythm-game__control-keys  .button{
        margin: 10px;
        width: 110px;
    }

    .rhythm-game__control-keys .button:first{
        margin-left: 0px;
    }

    .rhythm-game__keyboard-row_third .button{
        font-family: inherit;
    }

    .rhythm-game__keyboard-row_fourth .button{
        //width: 140px;
        //font-family: inherit;
    }

    .rhythm-game__keyboard-row_fourth .small-font-button{
        font-size: 15px;
    }

    .BPM-indicator{
        font-size: 16px;
    }
    .BPM-slider {
        width: 80px;
    }

    .norfolk{
        font-family: Norfolk;
    }

    .norfolk-note-button-text{
        font-family: Norfolk;

        font-size: 33px;
        margin-top: 19px;
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


    // Wide last button
    /*.rhythm-game__keyboard-row_fourth .button:last-child{
        width: 170px;
    }*/

    // For iPhone 6,7,8 screens
    @include breakpoint-phone{
        .button_1_col{
            width: 60px !important;
            height: 60px !important;
        }
    }

    /*@include breakpoint-large-phone{
        .rhythm-game__keyboard-row .button {
            margin-left: 12px;
        }

        .rhythm-game__keyboard-row {
            margin-bottom: 15px;
        }
    }*/
    
    @media only screen 
  and (max-device-height: 600px)
  and (-webkit-min-device-pixel-ratio: 2)
  and (orientation: landscape) {

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

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"
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

var Fraction = require('fraction.js');

export default {
    
    methods: {

        note(num) {

            this.key_callback({
                type: this.rest_mode ? 'r' : 'n',
                symbol: this.rest_mode ? num + 'r' : "" + num,
                duration: new Fraction(1).div(num)
            });

        },

        rest(num) {

            this.key_callback({
                type: 'r',
                symbol: num + 'r',
                duration: new Fraction(1).div(num)
            });

        },

        add_bar(){

            this.key_callback({
                type: 'bar',
                symbol: '4',
                duration: new Fraction(0)
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

        tuplet() {

            this.key_callback({
                type: 'tuplet',
                tuplet_type: 3
            });

        },


        keyboardClick(key) {

            this.key_callback();

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

        toggle_rest_mode(){
            this.rest_mode = !this.rest_mode;
        },

        note_text(type) {
            switch(type){
                case 2: return "h";
                case 4: return "q";
                case 8: return "e";
                case 16: return "s";
                case 32: return "32";
            }
        },

        rest_text(type) {
            switch(type){
                case 2: return "H";
                case 4: return "Q";
                case 8: return "E";
                case 16: return "S";
                case 32: return "32R";
            }
        },

        note_color() {
            return "green";
        },

        rest_color() {
            return "red";
        },

        hideNoteButtonWhenInTuplet(button_type){
            if(this.in_tuplet && button_type != this.cursor.tuplet_type){
                return true;
            }
            return false;
        },

    },
    components: {
        SexyButton, TwoRowsButton, SexySlider
    },
    data: function() {
        return {
            playback_throttle: 2,
            rest_mode: false,
            moving_buttons: false,

            buttons: false,

            bpm_slider: {
                value: 10
            }
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
        }

    }

}
</script>
