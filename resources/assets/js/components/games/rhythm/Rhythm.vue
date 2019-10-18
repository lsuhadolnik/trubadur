<template>
    <div class="rhythm-game__wrap">
        
        <loader v-show="displayState == 'loading' || (displayState == 'ready' && !soundLoaded)"></loader>

        <div class="window-resize__notification" v-if="opts.windowResizedNotify">
            <div class="window-resize__notification-title">
                Samo trenutek...
            </div>
            <div class="window-resize__notification-subtitle">
                <sexy-button text="OK" color="green" @click.native="rerenderStaffs" />
            </div>
        </div>

        <div class="rhythm__instructions" v-show="displayState == 'instructions'">
            <SexyButton @click.native="startGame()" color="green" :cols="3">Začni</SexyButton>
            <!--<ul class="rhythm__instructions-list">
                <li class="rhythm__instructions-list-item">Preizkusil/a se boš v ritmičnem nareku.</li>
                <li class="rhythm__instructions-list-item">Vaja bo v {{bar.num_beats}}/{{bar.base_note}} taktu.</li>
                <li class="rhythm__instructions-list-item">Slišal/a boš {{num_beats_text}}.</li>
                <li class="rhythm__instructions-list-item">Na začetku bo metronom izvajal en takt.</li>
                <li class="rhythm__instructions-list-item">Tempo bo {{questionState.exercise != null ? questionState.exercise.BPM : "??" }} udarcev na minuto.</li>
                <li class="rhythm__instructions-list-item">Za reševanje imaš na voljo {{questionState.maxSeconds}} sekund.</li>
                <li class="rhythm__instructions-list-item">Odgovor lahko preveriš največ {{questionState.maxChecks}}-krat.</li>
                <li class="rhythm__instructions-list-item">Če aplikacijo uporabljaš prvič, ali če ne veš, kako deluje kakšen gumb, pritisni gumb Pomoč.<br>Dobro je, da si Pomoč ogledaš pred prvo igro.<br>Med igro si pomoč lahko ogledaš s pritiskom na gumb Pomoč na tipkovnici.</li>
                <li class="rhythm__instructions-list-item" style="list-style-type: none;">
                    <sexy-button :text="metronomeButtonText" :color="metronomeButtonColor" :cols="3" @click.native="toggleMetronome()"/>
                    <sexy-button text="Pomoč" color="sunglow" :cols="3" @click.native="showHelp = true" />
                </li>
            </ul>-->
            <element-title class="instructions__title" text="Ritmični narek"></element-title>
            <div class="rhythm__fact-sheet">
                <div class="rhythm__fact-wrap">
                    <div class="rhythm__fact">
                        <div class="rhythm__fact-sheet__fact-top">{{bar.num_beats}}/{{bar.base_note}}</div>
                        <div class="rhythm__fact-sheet__fact-bottom">takt. nač.</div>
                    </div>
                    <div class="rhythm__fact">
                        <div class="rhythm__fact-sheet__fact-top">{{this.questionState.num_beats}}</div>
                        <div class="rhythm__fact-sheet__fact-bottom">{{num_beats_text}}</div>
                    </div>
                </div>
                <div class="rhythm__fact-wrap">
                    <div class="rhythm__fact">
                        <div class="rhythm__fact-sheet__fact-top">1 takt</div>
                        <div class="rhythm__fact-sheet__fact-bottom">metronoma</div>
                    </div>
                    <div class="rhythm__fact">
                        <div class="rhythm__fact-sheet__fact-top">{{questionState.exercise != null ? questionState.exercise.BPM : "??" }} <span style="font-size: 19px;">bpm</span></div>
                        <div class="rhythm__fact-sheet__fact-bottom">tempo</div>
                    </div>
                </div>
                <div class="rhythm__fact-wrap">
                    <div class="rhythm__fact">
                        <div class="rhythm__fact-sheet__fact-top">{{questionState.maxSeconds}}</div>
                        <div class="rhythm__fact-sheet__fact-bottom">sekund</div>
                    </div>
                    <div class="rhythm__fact">
                        <div class="rhythm__fact-sheet__fact-top">{{questionState.maxChecks}}</div>
                        <div class="rhythm__fact-sheet__fact-bottom">poskusov</div>
                    </div>
                </div>
            </div>
            <ul class="rhythm__instructions-list">
                <li class="rhythm__instructions-list-item">Če ne veš, kako uporabljati aplikacijo, pritisni gumb Pomoč.</li>
                <li class="rhythm__instructions-list-item" style="list-style-type: none;">
                    <sexy-button :text="metronomeButtonText" :color="metronomeButtonColor" :cols="3" @click.native="toggleMetronome()"/>
                    <sexy-button text="Pomoč" color="sunglow" :cols="3" @click.native="showHelp = true" />
                </li>
            </ul>
        </div>

        <KeyboardHelp v-if="showHelp" :hide="hideHelp" />

        <div class="ready-rhythm-game-view" v-show="displayState == 'ready' && soundLoaded">

            <div class="staff_view_wrap">
                <div class="staff_view_contents">
                    <StaffView ref="staff_view" :opts="opts" :bar="bar" :enabledContexts="['minimap', 'zoomview']" >

                        <div class="rhythm-game__staff__first-row">
                            <div id="first-row"></div>
                        </div>
                        
                        <div class="rhythm-game__staff__second-row">
                            <div id="second-row"></div>
                        </div>

                    </StaffView>
                </div>
                <div class="staff_view_time_slider" v-bind:style="{width: timeLeftPercents}">&nbsp;</div> 
            </div>
            
            <Keyboard ref="keyboard" v-bind="{key_callback: keyboard_click}" :playbackStatus="playbackStatus" :question="questionState" :say="showError" />

            <div class="error" v-show="errorMessage">{{errorMessage}}</div>

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

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';

    .rhythm__fact-sheet {
        list-style-type: none;
        display: flex;
        flex-wrap: wrap;
        padding: 0 0 0 0 !important;
    }

    .window-resize__notification {
        display: flex;
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: $golden-tainoi;
        z-index: 10;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .window-resize__notification-title {
        font-size: 30px;
        margin-bottom: 34px;
    }



    .rhythm__fact-wrap {
        width: 100%;
        display: flex;
    }

    .instructions__title {
        margin: 20px 0 20px 0;
    }

    .rhythm__fact-sheet__fact-top {
        font-size: 26px;
        text-align: center;
    }

    .rhythm__fact-sheet__fact-bottom {
        text-align: center;
    }

    .rhythm__fact {
        width: 50%;
        margin-bottom: 21px;
    }

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

import { mapState, mapGetters, mapActions, mapMutations } from 'vuex'

import { Howl, Howler } from 'howler-laravel-csrf';

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
            soundLoaded: false,

            questionState: {
                id: 0,
                check: "no", // "no", "correct", "wrong", "next", "waiting"
                wasCorrect: false,

                maxChecks: 12,
                maxSeconds: 60 * 10, // 10 minut
                
                num_beats: "x",
                number: 0,
                chapter: 1,
                exercise: null,
                statistics: {
                    nAdditions: 1, nDeletions: 1,
                    nPlaybacks: 1, nNoteTaps: 1,
                    nChecks: 1, startTime: 1,
                    duration: 0,
                    surrendered: false,
                    initialMetronome: true,
                }
            },

            opts: {
                windowResizedNotify: false,
                resizeTimeout: null,
            },

            countdownInterval: null,

            notes: null,
            bar: {
                num_beats: null,
                base_note: null,
                subdivisions: null
            },
            
            errorMessage: "",
            errorTimeout: null,

            showHelp: false,

            playbackStatus: {
                sound: null,
                metronome: true,
                BPM: 60,
                setBPM: this.setBPM,
                toggleMetronome: this.toggleMetronome,
                playing: false,
            },

            rhythmAudioSource: ""
        }
    },

    computed: {

        ...mapState(['me']),

        num_beats_text() {
            switch(this.questionState.num_beats){
                case 1:
                    return "takt";
                case 2:
                    return "takta";
                case 3:
                    return "takti";
                case 4:
                    return "takti";
                default:
                    return "taktov";
            }
        },

        metronomeButtonText() {
            if(this.playbackStatus.metronome){
                return "IZKLJUČI METRONOM";
            }
            return "VKLJUČI METRONOM";
        },

        metronomeButtonColor() {
            if(this.playbackStatus.metronome){
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
        ...mapMutations(['setHeaderMenuDisabled', 'toggleHeaderMenuDisabled']),

        showMetadataAlert() {

            alert("loadedmetadata")

        },

        checkIsSmallPhone(){

            let width  = window.screen.width;

            // and (min-device-width: 320px)
            // and (max-device-width: 568px)
            this.setHeaderMenuDisabled(width < 568);
        },

        setPlaying(v) {
            this.playbackStatus.playing = v;
        },

        toggleMetronome(reload) {
            this.playbackStatus.metronome = !this.playbackStatus.metronome;
            if(reload) {
                this.reloadAudio(true);
            }
            
        },

        setBPM(val, reload) {
            this.playbackStatus.BPM = val;
            if(reload) {
                this.reloadAudio(true);
            }
            
        },

        reloadAudio(play) {

            this.playbackStatus.sound.stop();

            let out = this;
            this.loadAudio(play).then(() => {
                out.displayState = "ready";
            });
        },

        setSoundBPM() {

            let newRate = this.playbackStatus.BPM / this.questionState.exercise.BPM;

            alert("SETTING BPM. New rate: " + newRate);

            if(this.playbackStatus.sound){
                
                this.playbackStatus.sound.rate(newRate);
            }

        },

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
            if(event.type == "surrender"){

                this.questionState.statistics.nChecks = this.questionState.maxChecks - 1;
                this.questionState.statistics.surrendered = true;
                this.check();

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
            else if(event.type == 'toggleMenu') {
                this.toggleHeaderMenuDisabled();
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

        rerenderStaffs() {
            
            if(this.opts.resizeTimeout) {
                clearTimeout(this.opts.resizeTimeout);
            }

            if(this.notes) {
                this.notes._call_render();
            }
            
            this.opts.windowResizedNotify = false;
        },

        loadInitialTrack() {

            this.displayState = "loading";
            let exid = this.questionState.exercise.id;
            this.soundLoaded = false;
            let out = this;

            return new Promise((resolve, reject) => {
                
                let baseURL = "/audio/base"+exid+".mp3";

                // Setup the new Howl.
                this.playbackStatus.sound = new Howl({
                    src: [ baseURL ],
                    format: [ 'mp3' ],
                    onload: () => {
                        out.soundLoaded = true;
                        resolve();
                    },
                    onloaderror: () => {
                        alert("NAPAKA PRI NALAGANJU VAJE!");
                        out.$router.push({name: 'dashboard'});
                    },
                    onplay:  () => { out.setPlaying(true) },
                    onpause: () => { out.setPlaying(false) },
                    onstop:  () => { out.setPlaying(false) },
                    onend:   () => { out.setPlaying(false) },
                });
                // Change global volume.
                Howler.volume(1);

            });
        },

        loadAudio(play){

            this.displayState = "loading";

            let exid = this.questionState.exercise.id;

            let out = this;

            return new Promise((resolve, reject) => {
                
                let baseURL = "/api/sound/"+exid+"?";
                let metEnabled = "metronome=" + this.playbackStatus.metronome;
                let bpmOverride = "bpm=" + this.playbackStatus.BPM;
                let url = baseURL + [metEnabled, bpmOverride].join('&');

                // Setup the new Howl.
                this.playbackStatus.sound = new Howl({
                    src: [ url ],
                    format: [ 'mp3' ],
                    onload: () => {
                        out.soundLoaded = true;
                        if(play) {
                            out.playbackStatus.sound.play();
                        }

                        resolve();

                    },
                    onloaderror: () => {
                        alert("NAPAKA PRI NALAGANJU VAJE!");
                        out.$router.push({name: 'dashboard'});
                    },
                    onplay:  () => { out.setPlaying(true) },
                    onpause: () => { out.setPlaying(false) },
                    onstop:  () => { out.setPlaying(false) },
                    onend:   () => { out.setPlaying(false) },
                });


                // Change global volume.
                Howler.volume(1);

            });
        },

        showDiff(nowDate){


            this.$refs.diff_view.render(
                this.questionState.exercise.notes,
                this.notes.notes,
                this.bar
            );

            this.displayState = "diff";
            
        },

        startCountdownInterval() {

            this.countdownInterval = setInterval(() => {
                    
                this.questionState.statistics.duration = Math.floor(((new Date().getTime()) - this.questionState.statistics.startTime) / 1000);

                let timeout = this.questionState.statistics.duration >= this.questionState.maxSeconds;
                if(timeout){

                    clearInterval(this.countdownInterval);
                    this.check();

                }

            }, 500);

        },

        startGame() {

            this.questionState.wasCorrect = false;
            
            // Nastavi statistiko metronoma na začetno vrednost, ki jo je uporabnik nastavil pri navodilih
            this.questionState.statistics.initialMetronome = this.playbackStatus.metronome;

            this.questionState.statistics.startTime = (new Date()).getTime();
            this.startCountdownInterval();

            this.displayState = "ready";
            return this.play({action: "replay", what: "exercise"});

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
            this.questionState.statistics.surrendered = false;
            this.questionState.statistics.metronome = true;

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

                this.prepareQuestionWithContent(question.id, exercise);
            });

        },

        prepareQuestionWithContent(questionId, exercise, play) {

            this.questionState.exercise = exercise;
            this.questionState.id = questionId;

            this.loadInitialTrack();

            this._questionState_reset();
            this.questionState.num_beats = util.get_bar_count(exercise.notes);
        
            this._copy_bar_info(exercise);

            this.setBPM(exercise.BPM ? exercise.BPM : 60);

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
                    metronome:   this.questionState.statistics.initialMetronome,
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

                if(status.canSubmit === false && !status.overcheck && !status.timeout){
                    alert("Poglej znova. Na črtovju ne sme biti modrih not.");
                }
                
                this.questionState.check = "wrong";
                setTimeout(function() {

                    if(outside.questionState.statistics.surrendered){
                        outside.questionState.check = "next";
                        outside.check();
                        return;
                    }

                    // Watch out, could happen when next question is already loaded
                    outside.questionState.check = "no";

                    if(status.overcheck || status.timeout){
                        outside.questionState.check = "next";
                        // outside.check();
                    }
                }, changeTimeout);
            }
        },

        check(){

            if(this.playbackStatus.sound.stop()) {
               this.playbackStatus.sound.stop();
            }

            if(this.questionState.check == "next"){
                this.showDiff();
                return;
            }

            if(this.questionState.check != "no"){
                return;
            }

            // Update statistics
            this.questionState.statistics.nChecks += 1;

            let canSubmit = this.notes.canSubmit();
            let status = util.check_notes_equal(this.questionState.exercise.notes, this.notes.notes) && canSubmit;
            let time = ((new Date()).getTime() - this.questionState.statistics.startTime);
            this.questionState.check = "waiting";


            this.questionState.wasCorrect = status;

            let outside = this;

            let timeout = this.questionState.statistics.duration >= this.questionState.maxSeconds;

            let checkStatus = {
                canSubmit,
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

            this.questionState.statistics.nPlaybacks += 1;

            if(event.action == "stop"){
                
                this.playbackStatus.sound.stop();
            }

            if(event.action == "replay"){
                
                this.playbackStatus.sound.play();
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
            

        },

        resizeNotify() { 
            this.opts.windowResizedNotify = true;
            if(this.opts.resizeTimeout){
                clearTimeout(this.opts.resizeTimeout);
            }
            this.opts.resizeTimeout = setTimeout(this.rerenderStaffs, 250);
        },
        

},

    beforeDestroy() {

        this.setHeaderMenuDisabled(false);
        window.removeEventListener('resize', this.checkIsSmallPhone);
        window.removeEventListener("resize", this.resizeNotify, false);
        window.removeEventListener("orientationchange", this.resizeNotify, false);
        

    },

    mounted() {

        let out = this;

        // Original
        this.$refs.staff_view.init({userName: "RhythmView", cursor: {enabled: true}});
        this.$refs.keyboard.init(this.$refs.staff_view.cursor);

        if(this.isExerciseTest()){
            // Override - show certain exercise and quit
            alert("FIX THE SOUND LOADING!");
            debugger;
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

            this.nextQuestion()
                .then(() => { this.displayState = "instructions"; return; });
            
        }

        // setInitial
        this.checkIsSmallPhone();

        window.addEventListener('resize', this.checkIsSmallPhone);

        window.addEventListener("resize", this.resizeNotify, false);
        window.addEventListener("orientationchange", this.resizeNotify, false);

    },
    
}
    

</script>