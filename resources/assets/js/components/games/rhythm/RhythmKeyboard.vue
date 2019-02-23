<template>

    <div class="rythm-game__keyboard_wrap">
        <div class="rhythm-game__event-keys">
            <div class="rhythm-game__keyboard">

                <div class="row rhythm-game__keyboard-row">
                    <sexy-button color="orange" @click.native="move_cursor_forward()" >
                        <span style="font-family: Gotham round;">
                            &gt;
                        </span>
                    </sexy-button>
                    <sexy-button :hidden="cursor.in_tuplet" :text="half_text" :color="note_color" @click.native="half_note()" />
                    <sexy-button :hidden="cursor.in_tuplet" :text="quarter_text" :color="note_color" @click.native="quarter_note()" />
                    <sexy-button :hidden="cursor.in_tuplet" :text="eight_text" :color="note_color" @click.native="eight_note()" />
                    <sexy-button :hidden="cursor.in_tuplet" :text="sixteenth_text" :color="note_color" @click.native="sixteenth_note()" />
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
                </div>

            </div>
        </div>
        <div class="row rhythm-game__control-keys">

            <input type="range" min="1"  value="2" max="3" step="0.2" v-model="playback_throttle">
            {{playback_throttle}}
            <sexy-button text="PREDVAJAJ" color="green" w="175px" @click.native="playback()"/>
                
        </div>
        <div class="row rhythm-game__control-keys">

            <sexy-button text="BRIŠI" color="green" w="175px" @click.native="delete_note()"/>
            <sexy-button text="PONOVI" color="green" w="175px" @click.native="repeat_exercise()" />
            <sexy-button text="PREVERI" color="orange" w="175px" @click.native="check()" />
                
        </div>
            
    </div>

</template>

<style lang="scss" scoped>

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
    }

    .rhythm-game__keyboard-row{
        
        margin-bottom: 5px;
    }

    .rhythm-game__keyboard-row .button{
        width: 50px;
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

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"
import TwoRowsButton from "../../elements/TwoRowsButton.vue"

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

        playback(){

            this.key_callback({
                type: 'play_user',
                throttle: this.playback_throttle
            });

        },

        repeat_exercise() {

            this.key_callback({
                type: 'play_exercise',
                throttle: this.playback_throttle
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
        };
    },
    props: [
        'key_callback', 'cursor'
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
