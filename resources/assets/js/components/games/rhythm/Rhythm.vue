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
                num_beats: 6,
                base_note: 8
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

            generator: new ExerciseGenerator(this.generate_playback_durations)
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

        get_duration_values(durations){
            
            // DEBUG
            let durationValues = [];
            durations.forEach((f) => { durationValues.push(f.toFraction()) });
            console.log(durationValues);

        },

        generate_playback_durations(values, get_val){

            // Negativna trajanja pomenijo pavze

            let nextHasTie = function(position){
                return values.length > position + 1 
                && values[position + 1].tie;
            }
            let nextIsRest = function(position){
                return values.length > position + 1 
                && values[position + 1].type == 'r';
            }
            let sumTiedDurations = function(cursorPosition){
                
                let duration = values[cursorPosition].duration;
                let numTies = 0;
                let pos = cursorPosition;
                
                while(nextHasTie(pos) && !nextIsRest(pos)){
                    duration = duration.add(values[pos + 1].duration);
                    numTies++; pos ++;
                }
                return {duration: duration, skips: numTies};

            }

            let realDurations = [];

            let skipN = 0;
            for(var noteIndex = 0; noteIndex < values.length; noteIndex++){
                
                if(skipN > 0){ skipN --; continue; }

                let note = values[noteIndex];

                if(note.type != "r")
                {
                    let vals = sumTiedDurations(noteIndex);   
                    skipN = vals.skips;
                    
                    if(!get_val)
                        realDurations.push(vals.duration);
                    else
                        realDurations.push(vals.duration.toFraction());

                }else {

                    if(!get_val)
                        realDurations.push(note.duration.mul(-1));
                    else
                        realDurations.push(note.duration.mul(-1).toFraction());
                }
            }

            // if(get_val){
            //     console.log(realDurations);
            // }else{
            //     console.log(this.get_duration_values(realDurations));
            // }

            return realDurations;

        },

        playback(values, throttle) {
            
            var currentTime = 0;
            let durations = this.generate_playback_durations(values);

            // this.get_duration_values(durations);

            let intensity = 127;

            for(var idx_duration = 0; idx_duration < durations.length; idx_duration++){

                let dur = durations[idx_duration];

                if(dur > 0)
                {
                    MIDI.noteOn(0, 60, intensity, currentTime);
                    
                    currentTime += dur.valueOf() * throttle;
                    MIDI.noteOff(0, 60, currentTime);

                }else {
                    currentTime += -dur.valueOf() * throttle;
                }
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