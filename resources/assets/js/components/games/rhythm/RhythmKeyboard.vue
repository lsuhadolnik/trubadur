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
                    <sexy-button text="h" color="green" @click.native="half_note()" />
                    <sexy-button text="q" color="green" @click.native="quarter_note()" />
                    <sexy-button text="e" color="green" @click.native="eight_note()" />
                    <sexy-button text="s" color="green" @click.native="sixteenth_note()" />
                </div>
                <div class="row rhythm-game__keyboard-row">
                    <sexy-button color="orange" @click.native="move_cursor_backwards()" >
                        <span style="font-family: Gotham round;">
                            &lt;
                        </span>
                    </sexy-button>
                    <sexy-button text="." color="green" @click.native="dot()" />
                    <sexy-button text="u" color="green" @click.native="tie()" />
                    <sexy-button text="T" color="green" @click.native="tuplet()" />
                </div>

            </div>
        </div>
        <div class="row rhythm-game__control-keys">

            <sexy-button text="BRIŠI" color="green" w="175px" @click.native="delete_note()"/>
            <sexy-button text="PONOVI" color="green" w="175px" />
            <sexy-button text="NADALJUJ" color="orange" w="175px" />
                
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
                type: 'n',
                symbol: 'w',
                duration: new Fraction(1)
            });

        },

        half_note(){

            this.key_callback({
                type: 'n',
                symbol: '2',
                duration: new Fraction(1).div(2)
            });

        },

        quarter_note(){

            this.key_callback({
                type: 'n',
                symbol: '4',
                duration: new Fraction(1).div(4)
            });

        },
        
        eight_note(){

            this.key_callback({
                type: 'n',
                symbol: '8',
                duration: new Fraction(1).div(8)
            });
            
        },

        sixteenth_note(){

            this.key_callback({
                type: 'n',
                symbol: '16',
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

        keyboardClick(key) {

            this.key_callback();

        }
    },
    components: {
        SexyButton, TwoRowsButton
    },
    props: [
        'key_callback'
    ]

}
</script>
