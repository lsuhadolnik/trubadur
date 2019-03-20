<template>
    <div class="rhythm-game__wrap">

        <!--<div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
        </div>-->
        
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" />
        
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

            errorMessage: "",

            generator: new ExerciseGenerator(),
            playback: new RhythmPlaybackEngine(this.bar),
        }
    },
    
    methods: {

        keyboard_click(event) {

            if(event.type == "check"){
                this.generator.check(this.notes.notes);
            }
            else if(event.type == "playback"){
                this.play(event);
            }
            else{

                // Invalidate playback cache
                // Pismo, dobr se sliši :D
                this.playback.stop();

                this.notes.handle_button(event)
            }
    
        },

        play(event){
            
            console.log("GOT EVENT: "+JSON.stringify(event));

            if(event.action == "resume"){

                //alert("Called resume");

                this.playback.play();

            }

            if(event.action == "pause"){

                this.playback.pause();

            }

            if(event.action == "replay"){

                if(event.what == "user"){

                    this.playback.load(this.notes.notes, "user");
                    this.playback.play();

                }
                else if(event.what == "exercise"){

                    this.playback.load(this.generator.currentExercise, "exercise");
                    this.playback.play(); 
                }

            }

        },

},

    mounted() {

        // Initialize note store
        this.notes = new NoteStore(
            this.bar,
            this.cursor,
            this.$refs.staff_view.render
        );

        // Init MIDI
        let instruments = {
            piano: {
                channel: 0,
                soundfont: 'acoustic_grand_piano',
                colume: 127
            },
            piano: {
                channel: 1,
                soundfont: 'xylophone',
                volume: 200
            }   
        };

        MIDI.loadPlugin({
            soundfontUrl: '/soundfonts/',
            instruments: ['acoustic_grand_piano', 'xylophone'],
            targetFormat: 'mp3',
            onsuccess: () => {
                for (var name in instruments) {
                    let instrument = instruments[name];
                    MIDI.setVolume(instrument.channel, instrument.volume);
                    MIDI.programChange(instrument.channel, MIDI.GM.byName[instrument.soundfont].number);
                }

                // Play initial exercise
                this.play({type:"playback", action: "replay", what: "exercise"});
            }
        });


        this.playback.bar_info = this.bar;
        

    },
    
}
    

</script>