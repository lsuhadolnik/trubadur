<template>
    <div class="rhythm-game__wrap">

        <!--<div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
        </div>-->
        
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" />
        
        <Keyboard v-bind="{key_callback: keyboard_click}" />


    </div>
</template>

<style lang="scss">

    @import '../../../../sass/variables/index';

    .rythm-game__wrap {
        touch-action: manipulation;
    }

    .header {

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
                position: 0,
                x: 0
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