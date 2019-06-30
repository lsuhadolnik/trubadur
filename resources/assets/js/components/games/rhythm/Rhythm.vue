<template>
    <div class="rhythm-game__wrap">
        
        <loader v-show="displayState == 'loading'"></loader>
        <div class="rhythm__instructions" v-show="displayState == 'instructions'">
            <SexyButton @click.native="startGame()" color="green" :cols="3">Začni</SexyButton>
            <ul class="rhythm__instructions-list">
                <li class="rhythm__instructions-list-item">Preizkusil se boš v ritmičnem nareku.</li>
                <li class="rhythm__instructions-list-item">Vaja bo v {{bar.num_beats}}/{{bar.base_note}} taktu.</li>
                <li class="rhythm__instructions-list-item">Slišal boš {{num_beats_text}}.</li>
                <li class="rhythm__instructions-list-item">Predvajalo se bo s hitrostjo {{playback.BPM}} udarcev na minuto.</li>
                <li class="rhythm__instructions-list-item">Program je v preizkusni fazi, zanekrat lahko preizkusiš par vpisanih vaj.</li>
            </ul>
        </div>

        <div class="ready-rhythm-game-view" v-show="displayState == 'ready'">

            <!--<div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
        </div>-->

            <StaffView ref="staff_view" :bar="bar" :cursor="cursor" />
            
            <Keyboard :cursor="cursor" v-bind="{key_callback: keyboard_click}" :playbackStatus="playback" :question="questionState" :say="showError" />

            <div class="error" v-show="errorMessage">{{errorMessage}}</div>

        </div>

        <div class="rhythm-diff-check-view" v-show="displayState == 'diff'">

            <DiffView ref="diff_view" :dismiss="continueGame"></DiffView>

        </div>

    </div>
</template>

<style lang="scss">

    @import '../../../../sass/variables/index';

    .rhythm__instructions {
        padding        : 20px 0;
        display        : flex;
        align-items    : center;
        flex-direction : column;
    }

    .rhythm__instructions-list-item { 
        padding: 8px 20px 8px 3px; 
    }

    .rythm-game__wrap {
        touch-action: manipulation;
    }

    .error{
        text-align: center;
        text-transform: uppercase;
        color: $neon-red;
        background: black;
    }

    .app {
        @include breakpoint-phone-landscape {
            padding-bottom: 0px !important;
        }
    }

    .header-menu{
        @include breakpoint-small-phone {
            display: none !important;
            position: static !important;
        }
    }

    .app--sticky{
        @include breakpoint-small-phone {
            padding: 0px !important;
        }
    }
    

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"
import CircleTimer from "../../elements/CircleTimer.vue"
import ProgressBar from "../../elements/ProgressBar.vue"

import StaffView from "./StaffView.vue"
import DiffView from "./DiffView.vue"
import Keyboard from "./RhythmKeyboard.vue"

import NoteStore from "./noteStore"
import ExerciseGenerator from './exerciseGenerator'
import RhythmPlaybackEngine from './rhythmPlaybackEngine'

import { mapState, mapGetters, mapActions } from 'vuex'

var Fraction = require('fraction.js');


export default {
    
    components: {
        SexyButton, CircleTimer, ProgressBar, StaffView, DiffView, Keyboard
    },

    props: ["game", "difficulty"],

    data() {

        return {

            isPractice: false,
            displayState: 'loading',

            questionState: {
                check: "no", // "no", "correct", "wrong", "next"
                numChecks: 0,
                num_beats: "x"
            },

            notes: null,
            bar: {
                num_beats: 4,
                base_note: 4
            },
            cursor: {
                position: 0,
                x: 0,
                in_tuplet: false,

                cursor_moved: this.cursor_moved,

                selection: null, 
                selectionMode: false,
                selectionSelected: false,

                clearSelection: this.clearSelection,
                toggleSelectionMode: this.toggleSelectionMode,

                editing_tuplet: false,
                editing_tuplet_index: -1
            },

            errorMessage: "",
            errorTimeout: null,

            generator: new ExerciseGenerator(),
            playback: new RhythmPlaybackEngine(this.bar),
        }
    },

    computed: {
        num_beats_text() {
            switch(this.questionState.num_beats){
                case 1:
                    return "en takt";
                case 2:
                    return "dva takta";
                case 3:
                    return "tri takte";
                case 4:
                    return "štiri takte";
                default:
                    return this.questionState.num_beats+ " taktov";
            }
        }
    },
    
    methods: {

        clearSelection(){

            this.cursor.selection = null;
            this.cursor.selectionSelected = false;
            this.cursor.selectionMode = false;

        },

        cursor_moved(pos, from){

            this.$refs.staff_view.cursor_moved(pos, from);

        },

        toggleSelectionMode(){

            if(this.cursor.selectionMode){

                    this.clearSelection();

            }else{

                if(this.notes.notes.length == 0) {
                    return;
                }

                this.cursor.selectionMode = true;
                this.cursor.selectionSelected = false;
                
                let pos = this.cursor.position - 1;
                if(pos < 0){
                    pos = 0;
                }

                else if(pos > this.notes.notes.length){
                    pos = this.notes.notes.length - 1;
                }

                this.cursor.selection = {
                    base: pos,
                    from: pos,
                    to: pos
                }

            }
        },

        keyboard_click(event) {

            if(event.type == "check"){
                this.check();
            }
            if(event.type == "pass"){
                this.questionState.check = "next";
            }
            if(event.type == "submit"){

                this.submitQuestion();
                
            }
            if(event.type == "selectionMode"){

                this.toggleSelectionMode();
                this.notes._call_render();

            }
            else if(event.type == "playback"){
                this.play(event);
            }
            else if(event.type == "showJson"){

                // Replacements:
                // 1.   },    ->  },\n\t\t\t
                // 2. ({"type":"bar".*},)    ->   \n\n\t\t\t$1\n\n
                // 3.   ,"    ->   , "

                let text = JSON.stringify(this.notes.notes)
                    .replace(/\[/, "\t\t\t")
                    .replace(/\]/, "")
                    .replace(/},/gi, "},\n\t\t\t")
                    .replace(/({"type":"bar".*},)/gi, "\n\n\t\t\t$1\n\n")
                    .replace(/":/gi, "\" :")
                    .replace(/,"/gi, ", \"");

                console.log(text);

            }
            else if(event.type == "changeSignature"){
                
                this.bar.num_beats = parseInt(prompt("Num_beats?"));
                this.bar.base_note = parseInt(prompt("Base_note?"));

                this.notes._call_render();

            }
            else{

                // Invalidate playback cache
                if(this.playback.status != "playing")
                    this.playback.stop();

                this.notes.handle_button(event)
            }
    
        },

        submitQuestion(){

            this.$refs.diff_view.render(
                    this.generator.currentExercise,
                    this.notes.notes,
                    this.bar
                );

            this.displayState = "diff";

        },

        startGame() {
            this.displayState = "ready";
            this.play({action: "replay", what: "exercise"});
        },

        nextQuestion(play){

            // Generate exercise
            this.generator.generate();
            this.bar.num_beats = this.generator.currentExerciseInfo.bar.num_beats;
            this.bar.base_note = this.generator.currentExerciseInfo.bar.base_note;
            
            let exerciseBPM = 120;
            if(this.generator.currentExerciseInfo.BPM){
                exerciseBPM = this.generator.currentExerciseInfo.BPM;
            }
            this.playback.BPM = exerciseBPM;
            this.playback.BPM_from = 50;
            this.playback.BPM_to = 240;

            // Initialize note store
            this.notes = new NoteStore(
                this.bar,
                this.cursor,
                this.$refs.staff_view.render
            );

            window.____notes = this.notes;

            this.questionState.check = "no";
            this.playback.bar_info = this.bar;

            if(play){
                this.play({action: "replay", what: "exercise"});
            }
            

        },

        continueGame(){

            debugger;

            this.nextQuestion(true);
            this.displayState = "ready";
            
            let out = this;
            setTimeout(function() {
                out.$refs.staff_view.reset();
            }, 100);

        },

        check(){

            if(this.questionState.check == "next"){
                this.submitQuestion();
                return;
            }

            let status = this.generator.check(this.notes.notes);

            let changeTimeout = 1000;
            let outside = this;

            if(status){
                
                this.questionState.check = "correct";
                setTimeout(function() {
                    // Watch out, could happen when next question is already loaded
                    outside.questionState.check = "next";
                }, changeTimeout);
            }else{
                this.questionState.check = "wrong";
                setTimeout(function() {
                    // Watch out, could happen when next question is already loaded
                    outside.questionState.check = "no";
                }, changeTimeout);
            }

        },

        play(event){

            if(event.action == "resume"){
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

        showError(err){
            this.errorMessage = err;

            if(this.errorTimeout){
                clearTimeout(this.errorTimeout);
                this.errorTimeout = null;
            }

            let outer = this;
            this.errorTimeout = setTimeout(function(){
                outer.showError("");                    
            }, 3000);
        }

},

    mounted() {

        // Če do sem nisi prišel preko vmesnika, 
        // greš lahko kar lepo nazaj
        /*if (!this.game || !this.difficulty) {
            this.$router.push({ name: 'dashboard' })
        }*/


        // Init MIDI
        let instruments = [
            {
                channel: 0,
                soundfont: 'percussive_organ',
                colume: 127
            },
            {
                channel: 1,
                soundfont: 'xylophone',
                volume: 200
            }   
        ];

        MIDI.loadPlugin({
            soundfontUrl: '/soundfonts/',
            instruments: instruments.map(e => e.soundfont),
            targetFormat: 'mp3',
            onsuccess: () => {
                for (let i = 0; i < instruments.length; i++) {
                    let instrument = instruments[i];
                    MIDI.setVolume(instrument.channel, instrument.volume);
                    MIDI.programChange(instrument.channel, MIDI.GM.byName[instrument.soundfont].number);
                }

                this.nextQuestion();
                this.questionState.num_beats = this.generator.get_bar_count();

                this.displayState = "instructions";
        
            }
        });



    },
    
}
    

</script>