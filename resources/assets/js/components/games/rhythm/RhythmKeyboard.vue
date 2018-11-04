<template>

    <div class="rythm-game__keyboard_wrap">
        <div class="rhythm-game__keyboard">

            <div class="row">
                <div class="col">
                    <two-rows-button></two-rows-button>
                </div>
                <div class="col">
                    <div class="row">
                        <sexy-button text="h" color="green" @click.native="half_note()" />
                        <sexy-button text="q" color="green" @click.native="quarter_note()" />
                        <sexy-button text="e" color="green" @click.native="eight_note()" />
                        <sexy-button text="s" color="green" @click.native="sixteenth_note()" />
                    </div>
                    <div class="row">
                        <sexy-button text="h" color="green" @click.native="dot()" />
                        <sexy-button text="q" color="green" @click.native="slur()" />
                        <sexy-button text="e" color="green" @click.native="tuplet()" />
                    </div>
                </div>
            </div>
            
                <!-- MusiSync staff font maps letters to note symbols... -->
                <div class="row rhythm-game__keyboard-row">
                    <sexy-button text="w" color="green" @click.native="whole_note()" />
                    <sexy-button text="h" color="green" @click.native="half_note()" />
                    <sexy-button text="q" color="green" @click.native="quarter_note()" />
                    <sexy-button text="e" color="green" @click.native="eight_note()" />
                    <sexy-button text="s" color="green" @click.native="sixteenth_note()" />
                </div>
                
                <div class="row rhythm-game__keyboard-row">
                    <sexy-button text="W" color="green" @click.native="keyboardClick('wr')" />
                    <sexy-button text="H" color="green" @click.native="keyboardClick('hr')" />
                    <sexy-button text="Q" color="green" @click.native="keyboardClick('qr')" />
                    <sexy-button text="E" color="green" @click.native="keyboardClick('er')" />
                    <sexy-button text="S" color="green" @click.native="keyboardClick('sr')" />
                </div>
                
                <div class="row rhythm-game__keyboard-row">
                    <sexy-button color="orange" @click.native="delete_note()">
                        <span style="padding-bottom:20px; font-family: Gotham round;">
                            DEL
                        </span>
                    </sexy-button>
                    <sexy-button color="green" @click.native="keyboardClick('dot')">
                        <span style="padding-bottom:30px">.</span>
                    </sexy-button>
                    <sexy-button color="green" @click.native="keyboardClick('divide2')">
                        <span style="padding-bottom:20px">/2</span>
                    </sexy-button>
                    <sexy-button color="green" @click.native="keyboardClick('divide3')">
                        <span style="padding-bottom:20px">/3</span>
                    </sexy-button>
                    
                </div>

        </div>

        <div class="row rhythm-game__control-keys">

            <sexy-button text="BRIŠI" color="green" w="175px" @click.native="delete_note()"/>
            <sexy-button text="PONOVI" color="green" w="175px" />
            <sexy-button text="NADALJUJ" color="orange" w="175px" />
                
        </div>
            
    </div>

</template>

<style lang="scss">

    .rhythm-game__keyboard {
        font-size: 20px;
        margin-bottom: 20px;
    }

    .rhythm-game__keyboard-row{
        display: flex;
        justify-content: center;
        margin-bottom: 15px;
    }

    .rhythm-game__keyboard-row .button{
        display: inline-block;
        width: 50px;
        //font-family: MusiSync;
        margin-left: 10px;
        //font-size: 40px;
    }

    .rhythm-game__control-keys{
        width: 100%;
        display: flex;
        bottom: 0;
        justify-content: center;

    }

    .rhythm-game__control-keys .button{
        display:inline-block;
        margin-left: 10px;
    }

    .rhythm-game__control-keys .button:first{
        margin-left: 0px;
    }

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"

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
        SexyButton
    },
    props: [
        'key_callback'
    ]

}
</script>
