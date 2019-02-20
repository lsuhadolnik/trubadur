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

import { mapState, mapGetters, mapActions } from 'vuex'

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

        let instruments = {
            piano: {
                channel: 0,
                soundfont: 'acoustic_grand_piano'
            }   
        };

        MIDI.loadPlugin({
            soundfontUrl: '/soundfonts/',
            instruments: ['acoustic_grand_piano'],
            targetFormat: 'mp3',
            onsuccess: () => {
                for (var name in instruments) {
                    let instrument = instruments[name];
                    MIDI.setVolume(instrument.channel, 127);
                    MIDI.programChange(instrument.channel, MIDI.GM.byName[instrument.soundfont].number);
                }
            }
        });
        

    },
    
}
    

</script>