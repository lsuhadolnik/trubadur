<template>
    <div class="rhythm-game__wrap">
        
        <loader v-show="displayState == 'loading'"></loader>
        <div class="rhythm__instructions" v-show="displayState == 'instructions'">
            <SexyButton @click.native="startGame()" color="green" :cols="3">Začni</SexyButton>
            <ul class="rhythm__instructions-list">
                <li class="rhythm__instructions-list-item">Preizkusila / preizkusil se boš v ritmičnem nareku.</li>
                <li class="rhythm__instructions-list-item">Vaja bo v {{bar.num_beats}}/{{bar.base_note}} taktu.</li>
                <li class="rhythm__instructions-list-item">Slišala / slišal boš {{num_beats_text}}.</li>
                <li class="rhythm__instructions-list-item">Na začetku bo metronom izvajal en takt.</li>
                <li class="rhythm__instructions-list-item">Tempo bo {{questionState.exercise != null ? questionState.exercise.BPM : "??" }} udarcev na minuto.</li>
                <li class="rhythm__instructions-list-item">Za reševanje imaš na voljo {{questionState.maxSeconds}} sekund.</li>
                <li class="rhythm__instructions-list-item">Odgovor lahko preveriš največ {{questionState.maxChecks}}-krat.</li>
                <li class="rhythm__instructions-list-item">Če ne veš, kako deluje kakšen gumb, pritisni gumb Pomoč.<br>Dobro je, da si Pomoč ogledaš pred prvo igro.</li>
                <li class="rhythm__instructions-list-item" style="list-style-type: none;">
                    <sexy-button :text="metronomeButtonText" :color="metronomeButtonColor" :cols="3" @click.native="playback.metronome = !playback.metronome"/>
                </li>
                <!--<li class="rhythm__instructions-list-item">Program je v preizkusni fazi, zanekrat lahko preizkusiš par vpisanih vaj.</li>-->
            </ul>
        </div>

        <div class="ready-rhythm-game-view" v-show="displayState == 'ready'">

            <!--<div class="rhythm-game__progress">
            <CircleTimer></CircleTimer>
            <ProgressBar></ProgressBar>
            </div>-->

            <div class="staff_view_wrap">
                <div class="staff_view_contents">
                    <StaffView ref="staff_view" :bar="bar" :enabledContexts="['minimap', 'zoomview']" >

                        <div class="rhythm-game__staff__first-row">
                            <div id="first-row"></div>
                        </div>
                        
                        <div class="rhythm-game__staff__second-row">
                            <div id="second-row"></div>
                        </div>
                        
                        <!--height: <input class="BPM-slider" type="range" :min="10" :max="100" step="1" v-model="info.height" v-on:mousemove="force_redraw()"> {{info.height}}
                        barHeight: <input class="BPM-slider" type="range" :min="10" :max="100" step="1" v-model="info.barHeight" v-on:mousemove="force_redraw()"> {{info.barHeight}}
                        barOffsetY: <input class="BPM-slider" type="range" :min="10" :max="100" step="1" v-model="info.barOffsetY" v-on:mousemove="force_redraw()"> {{info.barOffsetY}}
                        zoomViewHeight: <input class="BPM-slider" type="range" :min="10" :max="200" step="1" v-model="CTX.zoomview.containerHeight" v-on:mousemove="force_redraw()"> {{info.barOffsetY}}
                        --> 

                    </StaffView>
                </div>
                <div class="staff_view_time_slider" v-bind:style="{width: timeLeftPercents}">&nbsp;</div> 
            </div>
            
            <Keyboard ref="keyboard" v-bind="{key_callback: keyboard_click}" :playbackStatus="playback" :question="questionState" :say="showError" />

            <div class="error" v-show="errorMessage">{{errorMessage}}</div>

            <KeyboardHelp v-if="showHelp" :hide="hideHelp" />

            <div class="ready-rhythm-game-view__checkOverlay" v-if="['wrong', 'correct', 'waiting'].indexOf(questionState.check) > -1">
                <div class="ready-rhythm-game-view__checkOverlay__center" >
                    <div class="ready-rhythm-game-view__checkOverlay__center_bubble">
                        <div class="" v-if="questionState.check == 'wrong'">
                            <icon name="times"  scale="4"/>
                        </div>
                        <div class="timesLeft" v-if="questionState.check == 'wrong'">
                            {{ questionState.statistics.nChecks + "/" + questionState.maxChecks }}
                        </div>
                        <icon name="check" v-if="questionState.check == 'correct'" scale="4"/>
                        <icon name="refresh" v-if="questionState.check == 'waiting'" scale="4" spin />
                        <icon name="exclamation-circle" v-if="questionState.check == 'error'" />
                    </div>
                </div>
            </div>

        </div>

        <div class="rhythm-diff-check-view" v-show="displayState == 'diff'">

            <DiffView ref="diff_view" :success="questionState.wasCorrect" :dismiss="continueGame" :bar="bar" />

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

    .staff_view_wrap {
        position: relative;
    }

    .staff_view_contents {
        position: relative;
        z-index: 1;
    }

    .staff_view_time_slider {
        position: absolute;
        top: 0;
        left: 0;
        background: rgba(112,100,67,0.2);
        width: 100%;
        height: 100%;
        transition: width .1s ease-in;
    }

    .ready-rhythm-game-view__checkOverlay {
        position: absolute;
        display: block;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
        background: rgba(0,0,0,0.3);
        z-index: 100;
    }

    .ready-rhythm-game-view__checkOverlay__center {
        display: flex;
        justify-content: center;
        height: 100%;
        align-items: center;
    }

    .ready-rhythm-game-view__checkOverlay__center_bubble {

        width: 160px;
        height: 160px;
        background-color: rgba(0,0,0,0.6);
        border-radius: 5px;
        color: white;
        display: flex;
        justify-content: center;
        align-items: center;

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

        @include breakpoint-small-phone-landscape {
            display: none !important;
            position: static !important;
        }
    }

    .app--sticky{
        
            padding: 0px !important;
        
    }
    

</style>


<script>

import SexyButton from "../../elements/SexyButton.vue"
import CircleTimer from "../../elements/CircleTimer.vue"
import ProgressBar from "../../elements/ProgressBar.vue"

import StaffView from "./StaffView.vue"
import DiffView from "./DiffView.vue"
import Keyboard from "./Keyboard/RhythmKeyboard.vue"
import KeyboardHelp from "./Keyboard/KeyboardHelp.vue"

import NoteStore from "./noteStore"
import RhythmPlaybackEngine from './rhythmPlaybackEngine'

import { mapState, mapGetters, mapActions } from 'vuex'

const util = require('./rhythmUtilities');


export default {
    
    components: {
        SexyButton, CircleTimer, ProgressBar, StaffView, DiffView, Keyboard, KeyboardHelp
    },

    props: ["game", "difficulty"],

    data() {

        return {

            isPractice: false,
            displayState: 'loading',

            questionState: {
                id: 0,
                check: "no", // "no", "correct", "wrong", "next", "waiting"
                wasCorrect: false,

                maxChecks: 10,
                maxSeconds: 120,
                
                num_beats: "x",
                number: 0,
                chapter: 1,
                exercise: null,
                statistics: {
                    nAdditions: 1, nDeletions: 1,
                    nPlaybacks: 1, nNoteTaps: 1,
                    nChecks: 1, startTime: 1,
                    duration: 0
                }
            },

            countdownInterval: null,

            notes: null,
            bar: {
                num_beats: null,
                base_note: null,
                subdivisions: null
            },
            BPMobj: {
                BPM: 120
            },
            

            errorMessage: "",
            errorTimeout: null,

            showHelp: false,

            playback: new RhythmPlaybackEngine(),
            defaultBPM: 120,
        }
    },

    computed: {

        ...mapState(['me']),

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
        },

        metronomeButtonText() {
            if(this.playback.metronome){
                return "IZKLJUČI METRONOM";
            }
            return "VKLJUČI METRONOM";
        },

        metronomeButtonColor() {
            if(this.playback.metronome){
                return "green";
            }
            return "cabaret";
        },

        timeLeftPercents() {
            let seconds = 100 * (1 - (this.questionState.statistics.duration / this.questionState.maxSeconds));
            return seconds+"%";
        }
    },
    
    methods: {

        ...mapActions(['fetchMe', 'finishGameUser', 'completeBadges', 'generateQuestion', 'storeAnswer', 'fetchRhythmExercise', 'createRhythmExerciseFeedback']),


        cursor_moved(pos, from){

            this.$refs.staff_view.cursor_moved(pos, from);

        },

        keyboard_click(event) {

            if(event.type == "check"){
                this.check();
            }
            if(event.type == "pass"){
                this.questionState.check = "next";
            }
            if(event.type == "feedback"){
                this.openFeedbackWindow();
            }
            if(event.type == "submit"){

                this.showDiff();
            }
            if(event.type == "selectionMode"){

                this.$refs.staff_view.toggleSelectionMode();
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
            else if(event.type == "showHelp") {
                this.showHelp = true;
            }
            else if(event.type == "changeSignature"){
                
                this.bar.num_beats = parseInt(prompt("Num_beats?"));
                this.bar.base_note = parseInt(prompt("Base_note?"));

                this.notes._call_render();
            }
            else{

                this.notes.handle_button(event);

                if(["n", "r", "bar", "dot", "tie", "tuplet"].indexOf(event.type) >= 0){
                    this.questionState.statistics.nAdditions += 1;
                }

                if(["remove_tuplets", "delete"].indexOf(event.type) >= 0){
                    this.questionState.statistics.nDeletions += 1;
                }
            }
    
        },

        showDiff(nowDate){


            this.$refs.diff_view.render(
                this.questionState.exercise.notes,
                this.notes.notes,
                this.bar
            );

            this.displayState = "diff";
            
        },

        startGame() {

            this.questionState.wasCorrect = false;
            this.questionState.statistics.startTime = (new Date()).getTime();
            this.countdownInterval = setInterval(() => {
                
                this.questionState.statistics.duration = Math.floor(((new Date().getTime()) - this.questionState.statistics.startTime) / 1000);

                let timeout = this.questionState.statistics.duration >= this.questionState.maxSeconds;
                if(timeout){

                    clearInterval(this.countdownInterval);
                    this.check();

                }

            }, 500);

            this.displayState = "ready";
            this.play({action: "replay", what: "exercise"});
        },

        _copy_bar_info(exercise) {

            this.bar.num_beats = exercise.timeSignature.num_beats;
            this.bar.base_note = exercise.timeSignature.base_note;
            if(exercise.timeSignature.subdivisions){
                this.bar.subdivisions = exercise.timeSignature.subdivisions;
            }else {
                this.bar.subdivisions = null;
            }
        },

        _questionState_reset() {
            this.questionState.check = "no";
            this.questionState.wasCorrect = false;


            this.questionState.statistics.nAdditions = 1;
            this.questionState.statistics.nDeletions = 1;
            this.questionState.statistics.nPlaybacks = 1;
            this.questionState.statistics.nNoteTaps = 1;
            this.questionState.statistics.nChecks = 1;
            this.questionState.statistics.startTime = 1;
            this.questionState.statistics.duration = 1;

            clearInterval(this.countdownInterval);
        },

        _increment_question_number() {

            let maxQuestions = 2;
            let maxChapters = 1;

            // Next question.
            this.questionState.number += 1;

            // Is it the end of a chapter?
            if(this.questionState.number > maxQuestions) {
                
                // First question in a new chapter
                this.questionState.number = 1;
                this.questionState.chapter += 1;

                // Is it the end of a game?
                if(this.questionState.chapter > maxChapters) {
                    return "GAME";
                }

                return "CHAPTER";
            }

            return "QUESTION";

        },

        gameEnded() {
            
            return this.finishGameUser({ gameId: this.game.id, userId: this.me.id })
            .then(() => { return this.completeBadges(this.me.id) })
            .then(() => { this.$router.push({ name: 'gameStatistics', params: { id: this.game.id } })});

        },

        loadExerciseWithId(exerciseId) {

            this.displayState = 'loading';

            return this.fetchRhythmExercise(exerciseId)
            .then((exercise) => {
                return this.prepareQuestionWithContent(1, exercise, false);
            });

        },

        nextQuestion(play){

            this.displayState = 'loading';

            // Increment question and check if the game is over
            let skipType = this._increment_question_number();
            if(skipType == "GAME"){
                return this.gameEnded();
            }


            return this.generateQuestion(
                { 
                    game_id: this.game.id, 
                    number: this.questionState.number, 
                    chapter: this.questionState.chapter 
                })
            .then((question) => {

                let exercise = question.content;

                this.prepareQuestionWithContent(question.id, exercise)
            });

        },

        prepareQuestionWithContent(questionId, exercise, play) {

            this.questionState.exercise = exercise;
            this.questionState.id = questionId;

            this._questionState_reset();
            this.questionState.num_beats = util.get_bar_count(exercise.notes);
        
            this._copy_bar_info(exercise);

            this.playback.setBPM(exercise.BPM ? exercise.BPM : this.defaultBPM);
            this.playback.setBar(exercise.timeSignature);

            // Initialize note store
            this.notes = new NoteStore(
                this.bar,
                this.$refs.staff_view.cursor,
                this.$refs.staff_view.render
            );

            window.____notes = this.notes;

            
            if(play){
                this.play({action: "replay", what: "exercise"});
            }

            this.$refs.staff_view.reset();

        },

        continueGame(){

            if(this.isExerciseTest()){
                window.close();
                alert("Okno se bo zdaj zaprlo.");
                return;
            }

            this.nextQuestion(false).then((state) => {
                
                if(state == "GAME"){

                } else {
                    this.displayState = "instructions";
                }
                
                
            });
            
        },

        logAnswer(info) {

            return this.storeAnswer({ 
                    game_id: this.game.id, 
                    user_id: this.me.id, 
                    question_id: this.questionState.id, 
                    time: info.time, 
                    n_additions: this.questionState.statistics.nAdditions, 
                    n_deletions: this.questionState.statistics.nDeletions, 
                    n_playbacks: this.questionState.statistics.nPlaybacks, 
                    n_answers:   this.questionState.statistics.nChecks, 
                    success:  info.status})
                .catch(() => {

                    this.questionState.check = "error";

                });

        },

        updateCheckStatus(status) {
            let outside = this;
            let changeTimeout = 1000;

            if(status.success){
                        
                this.questionState.check = "correct";
                setTimeout(function() {
                    // Watch out, could happen when next question is already loaded
                    outside.questionState.check = "next";
                    // outside.check();
                }, changeTimeout);
            }
            else{
                this.questionState.check = "wrong";
                setTimeout(function() {

                    // Watch out, could happen when next question is already loaded
                    outside.questionState.check = "no";
                    // If 

                    if(status.overcheck || status.timeout){
                        outside.questionState.check = "next";
                        // outside.check();
                    }
                }, changeTimeout);
            }
        },

        check(){

            if(this.questionState.check == "next"){
                this.showDiff();
                return;
            }

            if(this.questionState.check != "no"){
                return;
            }

            // Update statistics
            this.questionState.statistics.nChecks += 1;

            let status = util.check_notes_equal(this.questionState.exercise.notes, this.notes.notes);
            let time = ((new Date()).getTime() - this.questionState.statistics.startTime);
            this.questionState.check = "waiting";


            this.questionState.wasCorrect = status;

            let outside = this;

            let timeout = this.questionState.statistics.duration >= this.questionState.maxSeconds;

            let checkStatus = {
                timeout, 
                overcheck: this.questionState.maxChecks <= this.questionState.statistics.nChecks,
                success: status
            }

            // Correct or last chance...
            if((status || timeout || this.questionState.maxChecks <= this.questionState.statistics.nChecks) && !this.isExerciseTest()){
                // Log the answer
                return this.logAnswer({time, status}).then(() => {
                    return outside.updateCheckStatus(checkStatus);
                })
            } else {
                return this.updateCheckStatus(checkStatus);
            }

        },

        play(event){

            console.log(event);

            this.questionState.statistics.nPlaybacks += 1;

            
            if(event.action == "stop"){
                this.playback.stop();
                return;
            }

            let values = [];

            if(event.action == "replay"){
                if(event.what == "user"){

                    values = this.notes.notes;
                }
                else if(event.what == "exercise"){


                    values = this.questionState.exercise.notes;
                }
            }
            
            this.playback.play(null, null, values);
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
        },

        hideHelp() {
            
            this.showHelp = false;
            
        },

        isExerciseTest(){
            return this.$route.params.exerciseId != null;
        },

        openFeedbackWindow(){

            let feedback = prompt("Kaj želite sporočiti?");

            if(feedback){
                this.createRhythmExerciseFeedback({
                    rhythm_exercise_id: this.questionState.exercise.id,
                    question_id: this.questionState.id,
                    content: feedback
                }).then(() => {
                    alert("Komentar uspešno posredovan.");
                }).catch(() => {
                    alert("Napaka pri pošiljanju komentarja. Poskusite znova.");
                })
            }
            

        }

},

    mounted() {

        let out = this;

        // Original
        this.$refs.staff_view.init({userName: "RhythmView", cursor: {enabled: true}});
        this.$refs.keyboard.init(this.$refs.staff_view.cursor);

        if(this.isExerciseTest()){
            // Override - show certain exercise and quit

            this.fetchMe()
            .then(() => { return this.loadExerciseWithId(out.$route.params.exerciseId); })
            .then(() => { this.displayState = "instructions"; return false;});

            return;
        }

        // Če do sem nisi prišel preko vmesnika, 
        // greš lahko kar lepo nazaj
        if (!this.game || !this.difficulty) {
            this.$router.push({ name: 'dashboard' })
        } else {

            this.fetchMe()
            .then(() => { return this.nextQuestion(); })
            .then(() => { this.displayState = "instructions"; return; });
            
        }

        // DEBUG
        
        /*this.game = {id: 403};

        this.$refs.staff_view.init({userName: "RhythmView", cursor: {enabled: true}});
        this.$refs.keyboard.init(this.$refs.staff_view.cursor);

        

        this.fetchMe()
            .then(() => { return this.nextQuestion(); })
            .then(() => { this.startGame(); });
        */
        

    },
    
}
    

</script>