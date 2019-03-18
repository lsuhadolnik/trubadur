<template>

    <div class="rythm-game__keyboard_wrap">
        <div class="rhythm-game__event-keys">
            <div class="rhythm-game__keyboard">

                <div class="row rhythm-game__keyboard-row" >
                    <sexy-button color="orange" @click.native="move_cursor_forward()" >
                        <span style="font-family: Gotham round;">
                            &gt;
                        </span>
                    </sexy-button>
                    <sexy-button :hidden="cursor.in_tuplet" :text="half_text" :color="note_color" @click.native="half_note()" />
                    <sexy-button :hidden="cursor.in_tuplet" :text="quarter_text" :color="note_color" @click.native="quarter_note()" />
                    <sexy-button :hidden="cursor.in_tuplet" :text="eight_text" :color="note_color" @click.native="eight_note()" />
                    <sexy-button :hidden="cursor.in_tuplet" :text="sixteenth_text" :color="note_color" @click.native="sixteenth_note()" />
                    <div class="hide-normal"><!-- Will show only on small wide screens (landscape phones) -->
                        <sexy-button     color="sunglow" @click.native="repeat_exercise()" class="hide-normal">
                            <icon name="repeat" />
                        </sexy-button>
                        <sexy-button color="sunglow" @click.native="playback()" class="hide-normal">
                            <icon label="user-check">
                                <icon name="play" />
                            </icon>
                        </sexy-button>
                    </div>
                </div>
                <div class="row rhythm-game__keyboard-row">
                    <sexy-button color="orange" @click.native="move_cursor_backwards()" >
                        <span style="font-family: Gotham round;">
                            &lt;
                        </span>
                    </sexy-button>
                    <sexy-button :hidden="cursor.in_tuplet" :text="rest_mode_button_text" :color="rest_mode_button_color" @click.native="toggle_rest_mode()" />
                    <sexy-button :hidden="cursor.in_tuplet" text="." color="green" @click.native="dot()" />
                    <sexy-button text="u" color="green" @click.native="tie()" />
                    <sexy-button text="T" color="green" @click.native="tuplet()" />
                    <div class="hide-normal"> <!-- Will show only on small wide screens (landscape phones) -->
                        <sexy-button color="cabaret"   @click.native="delete_note()" >
                            <img src="/images/backspace.svg" width="30" />
                        </sexy-button>
                        <sexy-button color="cabaret" @click.native="check()">
                            <icon name="question-circle" />
                        </sexy-button>
                    </div>
                </div>
                <div class="row rhythm-game__keyboard-row rhythm-game__keyboard-row_fourth show-normal">
                    
                    <sexy-button color="sunglow" @click.native="play_exercise()">
                        <div class="small-font-button">Ponovi vajo</div>
                    </sexy-button>
                    
                    <sexy-button color="sunglow" @click.native="play_user()">
                        <div class="small-font-button">Zaigraj vpisano</div>
                    </sexy-button>

                    
                </div>
                <div class="row rhythm-game__keyboard-row rhythm-game__keyboard-row_third show-normal">
                    <sexy-button color="cabaret"   @click.native="delete_note()"  >
                        <img src="/images/backspace.svg" width="30" />
                    </sexy-button>
                    <!--<sexy-button :hidden="true" />
                    <sexy-button :hidden="true" />-->
                    <sexy-button color="cabaret" @click.native="resume()" v-if="!playbackStatus.playing && !playbackStatus.loaded">
                        <icon name="play" />
                    </sexy-button>

                    <sexy-button color="cabaret" @click.native="resume()" v-if="!playbackStatus.playing && playbackStatus.loaded">
                        <icon name="repeat" />
                    </sexy-button>

                    <sexy-button v-else color="sunglow" @click.native="pause()">
                        <icon name="pause" />
                    </sexy-button>
                    <sexy-button color="cabaret" customClass="wideButton" :cols="2">
                        <input type="range" min="1"  value="2" max="3" step="0.2" v-model="playback_throttle">
                    </sexy-button>
                    <sexy-button color="cabaret" @click.native="check()" >
                        <icon name="question-circle" />
                    </sexy-button>
                </div>

            </div>
        </div>
        <div class="row rhythm-game__control-keys">

            
            <!--
                {{playback_throttle}}
            <sexy-button text="PREDVAJAJ" color="green" w="175px" @click.native="playback()"/>
            -->
                
        </div>
        <!--<div class="row rhythm-game__control-keys">

            <sexy-button text="BRIŠI" color="green" w="175px" @click.native="delete_note()"/>
            <sexy-button text="PONOVI" color="green" w="175px" @click.native="repeat_exercise()" />
            <sexy-button text="PREVERI" color="orange" w="175px" @click.native="check()" />
                
        </div>-->
            
    </div>

</template>

<style lang="scss" scoped>

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

    
    .rythm-game__keyboard_wrap {
        
        padding: 0 10px 0 10px;
    }

    .rhythm-game__keyboard {
        font-size: 20px;
        margin-bottom: 20px;
        touch-action: manipulation;
    }

    .rhythm-game__keyboard-row{
        
        margin-bottom: 5px;
    }

    .rhythm-game__keyboard-row .button{
        //width: 50px;
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
        //width: 64.5px; // A little wider buttons

    }

    .rhythm-game__keyboard-row_fourth .button{
        width: 140px;
        font-family: inherit;
    }

    .rhythm-game__keyboard-row_fourth .small-font-button{
        font-size: 15px;
    }

    .rhythm-game__keyboard-row_third input{
        width: 80px;
    }


    // Wide last button
    /*.rhythm-game__keyboard-row_fourth .button:last-child{
        width: 170px;
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

        .rhythm-game__staff__second-row{
            margin-bottom: -27px !important;
        }

    }

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"
import TwoRowsButton from "../../elements/TwoRowsButton.vue"

import 'vue-awesome/icons/repeat'
import 'vue-awesome/icons/play'
import 'vue-awesome/icons/question-circle'
import 'vue-awesome/icons/user-o'
import 'vue-awesome/icons/pause'

var Fraction = require('fraction.js');

export default {
    
    methods: {

        whole_note(){

            this.key_callback({
                type: this.rest_mode ? 'r' : 'n',
                symbol: this.rest_mode ? 'wr' : 'w',
                duration: new Fraction(1)
            });

        },

        half_note(){

            this.key_callback({
                type: this.rest_mode ? 'r' : 'n',
                symbol: this.rest_mode ? '2r' : '2',
                duration: new Fraction(1).div(2)
            });

        },

        quarter_note(){

            this.key_callback({
                type: this.rest_mode ? 'r' : 'n',
                symbol: this.rest_mode ? '4r' : '4',
                duration: new Fraction(1).div(4)
            });

        },
        
        eight_note(){

            this.key_callback({
                type: this.rest_mode ? 'r' : 'n',
                symbol: this.rest_mode ? '8r' : '8',
                duration: new Fraction(1).div(8)
            });
            
        },

        sixteenth_note(){

            this.key_callback({
                type: this.rest_mode ? 'r' : 'n',
                symbol: this.rest_mode ? '16r' : '16',
                duration: new Fraction(1).div(16)
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

        play_user(){

            this.key_callback({
                type: 'playback',
                action: 'replay',
                what: 'user'
            });

        },

        play_exercise() {
            this.key_callback({
                type: 'playback',
                action: 'replay',
                what: 'exercise'
            });
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

        toggle_rest_mode(){

            this.rest_mode = !this.rest_mode;

        }
    },
    components: {
        SexyButton, TwoRowsButton
    },
    data: function() {
        return {
            playback_throttle: 2,
            rest_mode: false,

            buttons: false,
        };
    },
    props: [
        'key_callback', 'cursor', 'playbackStatus'
    ],
    computed: {
        note_color: function(){
            if(this.rest_mode) return "red";
            else return "green";
        },
        half_text: function(){
            if(this.rest_mode) return "H";
            else return "h";
        },
        quarter_text: function(){
            if(this.rest_mode) return "Q";
            else return "q";
        },
        eight_text: function(){
            if(this.rest_mode) return "E";
            else return "e";
        },
        sixteenth_text: function(){
            if(this.rest_mode) return "S";
            else return "s";
        },
        rest_mode_button_color: function(){
            if(this.rest_mode) return "green";
            else return "red";
        },
        rest_mode_button_text: function(){
            if(this.rest_mode) return "es";
            else return "ES"
        }
    }

}
</script>
