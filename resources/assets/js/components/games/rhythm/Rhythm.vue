<template>
    <div class="rhythm-game__wrap">

        <div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
        </div>
        
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" />
        
        <Keyboard v-bind="{key_callback: keyboard_click}" />

        <div class="rhythm-game__control-keys">

            <sexy-button text="BRIŠI" color="green" w="175px"/>
            <sexy-button text="PONOVI" color="green" w="175px" />
            <sexy-button text="NADALJUJ" color="orange" w="175px" />
            
        </div>


    </div>
</template>

<style lang="scss">

    @import '../../../../sass/variables/index';

    .rythm-game__wrap {
        touch-action: manipulation;
    }

    .rhythm-game__staff {
        display: flex;
        justify-content: center;
        
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
import CircleTimer from "../../elements/CircleTimer.vue"
import ProgressBar from "../../elements/ProgressBar.vue"

import StaffView from "./StaffView.vue"
import Keyboard from "./RhythmKeyboard.vue"

import NoteStore from "./noteStore"

export default {
    
    components: {
        SexyButton, CircleTimer, ProgressBar, StaffView, Keyboard
    },

    data() {
        return {
            notes: null,
            bar: {
                num_beats: 4,
                base_note: 4
            },
            cursor: {
                position: 0
            }
        }
    },
    
    methods: {
        keyboard_click(event) {
            this.notes.handle_button(event)
        }
    },

    mounted() {
        this.notes = new NoteStore(
            this.bar,
            this.cursor,
            this.$refs.staff_view.render
        );
    }
}
    

</script>