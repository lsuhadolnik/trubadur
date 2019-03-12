<template>
    <div class="rhythm-game__wrap">

        <!--<div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
        </div>-->
        
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" :staveCount="info.staveCount" />
        
        <Keyboard :cursor="cursor" v-bind="{key_callback: keyboard_click}" :playbackStatus="playback" />

        <div class="error" v-show="errorMessage">{{errorMessage}}</div>


    </div>
</template>

<style lang="scss">

    @import '../../../../sass/variables/index';

    .rythm-game__wrap {
        touch-action: manipulation;
    }

    .error{
        text-align: center;
        text-transform: uppercase;
        color: $neon-red;
        background: black;
    }
    

    @media only screen and (max-height: 600px) {
        
        .header-menu{
            display: none !important;
        }

    }

</style>


<script>


import SexyButton from "../../elements/SexyButton.vue"
import CircleTimer from "../../elements/CircleTimer.vue"
import ProgressBar from "../../elements/ProgressBar.vue"

import StaffView from "./StaffView.vue"
import Keyboard from "./RhythmKeyboard.vue"

import NoteStore from "./noteStore"
import ExerciseGenerator from './exerciseGenerator'
import RhythmPlaybackEngine from './rhythmPlaybackEngine'

import { mapState, mapGetters, mapActions } from 'vuex'

var Fraction = require('fraction.js');


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
                x: 0,
                in_tuplet: false,
            },
            info: {
                staveCount: 2,
            },

            errorMessage: "",

            generator: new ExerciseGenerator(this.generate_playback_durations),
            playback: new RhythmPlaybackEngine()
        }
    },
    
    methods: {

        keyboard_click(event) {

            if(event.type == "check"){
                this.generator.check(this.notes.notes);
            }
            else if(event.type == "play_user"){
                this.play_user(event);
            }
            else if(event.type == "play_exercise"){
                this.play_exercise(event);
            }
            else{
                this.notes.handle_button(event)
            }
    
        },

        play_exercise(event) {
            let throttle = 2.6;
            if(event && event.throttle){
                throttle = event.throttle;
            }
            //this.playback(this.generator.currentExercise, throttle);

            this.playback.load(this.generator.currentExercise);
            this.playback.play(); 
        },

        play_user(event) {
            //this.playback(this.notes.notes, event.throttle);
            this.playback.load(this.notes.notes);
            this.playback.play(); 
        },

        get_duration_values(durations){
            
            // DEBUG
            let durationValues = [];
            durations.forEach((f) => { durationValues.push(f.toFraction()) });
            console.log(durationValues);

        },

},

    mounted() {

        this.notes = new NoteStore(
            this.bar,
            this.cursor,
            this.$refs.staff_view.render,
            this.info
        );

        let instruments = {
            piano: {
                channel: 0,
                soundfont: 'acoustic_grand_piano'
            }   
        };

        MIDI.loadPlugin({
            soundfontUrl: '/soundfonts/',
            instruments: ['acoustic_grand_piano', 'xylophone'],
            targetFormat: 'mp3',
            onsuccess: () => {
                for (var name in instruments) {
                    let instrument = instruments[name];
                    MIDI.setVolume(instrument.channel, 127);
                    MIDI.programChange(instrument.channel, MIDI.GM.byName[instrument.soundfont].number);
                }

                // Play initial exercise
                this.play_exercise();
            }
        });
        

    },
    
}
    

</script>