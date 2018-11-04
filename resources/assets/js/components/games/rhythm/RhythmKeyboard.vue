<template>

<div class="rhythm-game__keyboard">

            
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
        font-family: MusiSync;
        margin-left: 10px;
        font-size: 40px;
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
