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

    .rhythm-game__wrap {
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
import RhythmPlaybackEngine from './rhythmPlaybackEngine'

import { mapState, mapGetters, mapActions } from 'vuex'

var Fraction = require('fraction.js');

const util = require('./rhythmUtilities');


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
                num_beats: "x",
                number: 1,
                chapter: 1,
                exercise: null
            },

            notes: null,
            bar: {
                num_beats: null,
                base_note: null,
                subdivisions: null
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

            playback: new RhythmPlaybackEngine(),
            defaultBPM: 120
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

        ...mapActions(['fetchMe', 'finishGameUser', 'completeBadges', 'generateQuestion', 'storeAnswer', 'setupMidi']),

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
                this.questionState.exercise.notes,
                this.notes.notes,
                this.bar
            );

            this.displayState = "diff";
        },

        startGame() {
            this.displayState = "ready";
            this.play({action: "replay", what: "exercise"});
        },

        _copy_bar_info(exercise) {

            this.bar.num_beats = exercise.bar.num_beats;
            this.bar.base_note = exercise.bar.base_note;
            if(exercise.bar.subdivisions){
                this.bar.subdivisions = exercise.bar.subdivisions;
            }

        },

        _questionState_reset() {
            this.questionState.check = "no";
        },

        nextQuestion(play){

            return this.generateQuestion(
                { 
                    game_id: this.game.id, 
                    number: this.questionState.number, 
                    chapter: this.questionState.chapter 
                })
            .then((question) => {
                        
                let exercise = JSON.parse(question.content);

                this.questionState.exercise = exercise;

                this._questionState_reset();
                this.questionState.num_beats = util.get_bar_count(exercise.notes);
                
                this._copy_bar_info(exercise);

                this.playback.setBPM(exercise.BPM ? exercise.BPM : this.defaultBPM);
                this.playback.setBar(exercise.bar);

                // Initialize note store
                this.notes = new NoteStore(
                    this.bar,
                    this.cursor,
                    this.$refs.staff_view.render
                );

                window.____notes = this.notes;

                
                if(play){
                    this.play({action: "replay", what: "exercise"});
                }

            });

        },

        continueGame(){

            this.nextQuestion(true).then(() => {

                this.displayState = "ready";
            
                let out = this;
                setTimeout(function() {
                    out.$refs.staff_view.reset();
                }, 100);

            });
            
        },

        check(){

            if(this.questionState.check == "next"){
                this.submitQuestion();
                return;
            }

            let status = util.check_notes_equal(this.questionState.exercise.notes, this.notes.notes);

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

                    this.playback.load(this.questionState.exercise.notes, "exercise");
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
        if (!this.game || !this.difficulty) {
            this.$router.push({ name: 'dashboard' })
        } else {

            this.fetchMe()
            .then(() => { return this.setupMidi(true, ['xylophone', 'percussive_organ']); })
            .then(() => { return this.nextQuestion(); })
            .then(() => { this.displayState = "instructions"; return; });
        }

    },
    
}
    

</script>