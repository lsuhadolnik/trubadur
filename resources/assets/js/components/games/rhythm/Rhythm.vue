<template>
    <div class="rhythm-game__wrap">

        <!--<div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
        </div>-->
        
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" :staveCount="info.staveCount" />
        
        <Keyboard :cursor="cursor" v-bind="{key_callback: keyboard_click}" />

        <div class="error" v-show="errorMessage">{{errorMessage}}</div>


    </div>
</template>

<style lang="scss" scoped>

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
import ExerciseGenerator from './exerciseGenerator'

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

            generator: new ExerciseGenerator()
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
            this.playback(this.generator.currentExercise, throttle);
        },

        play_user(event) {
            this.playback(this.notes.notes, event.throttle);
        },

        playback(values, throttle) {
            
            var currentTime = 0;

            var allDurations = []; var ssum = 0;
            for(var noteIndex = 0; noteIndex < values.length; noteIndex++){
                allDurations.push(values[noteIndex].duration.valueOf());
                ssum += values[noteIndex].duration.valueOf();
            }
            //console.log(allDurations);
            //console.log(ssum);

            let nextNoteExists = function(number){
                return values.length < number;
            }
            let nextHasTie = function(number){
                return values.length < number + 1 
                && values[number + 1].tie;
            }

            for(var noteIndex = 0; noteIndex < values.length; noteIndex++){
                
                let note = values[noteIndex];
                let noteValue = note.duration.valueOf() * throttle;

                var intensity = 127;
                if(nextHasTie()){
                    intensity = 256;
                }

                if(note.type != "r" && !note.tie)
                {
                    MIDI.noteOn(0, 60, intensity, currentTime);
                }
                
                if(!nextHasTie(noteIndex))             
                    MIDI.noteOff(0, 60, currentTime + noteValue);
                

                currentTime += noteValue;
            }

        
        
        }
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
            instruments: ['acoustic_grand_piano'],
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