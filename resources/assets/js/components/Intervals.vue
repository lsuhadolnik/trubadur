<style lang="scss" scoped>
@import '../../sass/app';

.intervals {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}

.intervals__loader {
    position  : absolute;
    top       : 50%;
    left      : 50%;
    transform : translate(-50%, -50%);
    width     : 100px;
    height    : 100px;

    @include breakpoint-phone {
        width  : 75px;
        height : 75px;
    }
}

.intervals__progress-wrapper {
    padding-top     : 25px;
    display         : flex;
    justify-content : space-evenly;
    align-items     : center;
}

.intervals__progress {
    color       : $black;
    font-family : $font-title;
    font-size   : 40px;
}

.intervals__stave-keyboard-wrapper {
    padding         : 25px 2.5vw;
    display         : flex;
    justify-content : center;
    align-items     : center;
    flex-direction  : row;

    @include breakpoint-portrait { flex-direction: column; }
}

.intervals__stave {
    margin-right: 2.5vw;

    @include-breakpoint-portrait { margin-right: 0; }
}

.intervals__keyboard {
    margin-left: 2.5vw;

    @include breakpoint-portrait {
        margin-top  : 5vh;
        margin-left : 0;
    }
}

.intervals__command-wrapper { display: flex; }

.intervals__command {
    width                 : calc(100vw / 3);
    height                : 50px;
    padding-top           : 5px;
    display               : flex;
    justify-content       : center;
    align-items           : center;
    font-size             : 20px;
    font-family           : $font-title;
    color                 : $black;
    opacity               : 0.8;
    -webkit-touch-callout : none;
    -webkit-user-select   : none;
    -khtml-user-select    : none;
    -moz-user-select      : none;
    -ms-user-select       : none;
    user-select           : none;
    cursor                : pointer;
    transition            : opacity 0.1s linear;

    @include breakpoint-tablet { opacity : 1; }
    @include breakpoint-phone  { opacity : 1; }

    &:hover { opacity: 1; }

    &.disabled {
        opacity : 1;
        filter  : brightness(1.5);
        cursor  : not-allowed;
    }
}

.intervals__command--delete { background-color : $neon-red; }
.intervals__command--replay { background-color : $blue;     }
.intervals__command--next   { background-color : $green;    }

.intervals__notification {
    margin-top      : 20px;
    display         : flex;
    justify-content : center;
    color           : $neon-red;
    font-family     : $font-light;
    font-size       : 18px;
}

.debug {
    display     : flex;
    color       : $dolphin;
    font-family : $font-light;
    font-size   : 15px;
}
</style>

<template>
    <div class="intervals">
        <img class="intervals__loader" src="/images/loader.svg" v-show="loading"/>
        <div v-show="!loading">
            <i
            <div class="intervals__progress-wrapper">
                <div class="intervals__progress">{{ chapter }} / {{ nChapters }}</div>
                <div class="intervals__progress">{{ number }} / {{ nQuestions }}</div>
                <svg id="timer"></svg>
            </div>
            <div class="debug">
                {{ sample.join(',') }} | {{ answer.map(a => a.pitch).join(',') }}
            </div>
            <div class="intervals__stave-keyboard-wrapper">
                <div class="intervals__stave">
                    <stave :n-notes="sample.length" :note-type="notes.type" :clef="notes.clef" :sharp-flat-map="sharpFlatMap" @notes-changed="notesChanged"></stave>
                </div>
                <div class="intervals__keyboard">
                    <keyboard :channel="channel" :midi="midi" @note-played="addNote"></keyboard>
                </div>
            </div>
            <div class="intervals__command-wrapper">
                <div class="intervals__command intervals__command--delete" :class="{ 'disabled': answer.length <= 1 }" @click="removeNote">IZBRIŠI NOTO</div>
                <div class="intervals__command intervals__command--replay" :class="{ 'disabled': playing }" @click="playNotes">PREDVAJAJ</div>
                <div class="intervals__command intervals__command--next" @click="checkCorrectness">NAPREJ</div>
            </div>
            <div class="intervals__notification">{{ notification }}</div>
        </div>
    </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'
import Stave from './music/Stave'
import Keyboard from './music/Keyboard.vue'

export default {
    props: ['game'],
    data () {
        return {
            loading: true,
            playing: false,
            notes: {
                type: 'whole',
                delay: 0,
                min: 0,
                max: 0,
                clef: ''
            },
            channel: 0,
            sharpFlatMap: {
                'A#3': 'Bb3',
                'Bb3': 'A#3',
                'C#4': 'Db4',
                'Db4': 'C#4',
                'D#4': 'Eb4',
                'Eb4': 'D#4',
                'F#4': 'Gb4',
                'Gb4': 'F#4',
                'G#4': 'Ab4',
                'Ab4': 'G#4',
                'A#4': 'Bb4',
                'Bb4': 'A#4',
                'C#5': 'Db5',
                'Db5': 'C#5'
            },
            nChapters: 3,
            nQuestions: 1,
            maxTimePerQuestion: 120000,
            timer: null,
            chapter: 1,
            number: 1,
            questionId: 0,
            sample: [],
            answer: [],
            startTime: 0,
            timeoutId: 0,
            nAdditions: 0,
            nDeletions: 0,
            nPlaybacks: 0,
            notification: ''
        }
    },
    created () {
        if (!this.game) {
            this.$router.push({ name: 'dashboard' })
        } else {
            this.setupMidi().then(() => {
                this.fetchMe().then(() => {
                    this.notes.delay = this.me.note_playback_delay
                    this.notes.clef = this.me.clef
                    this.channel = this.getInstrumentChannel(this.me.instrument)

                    this.nextQuestion()
                })
            })
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.timer = new TimerProgress({ // eslint-disable-line no-undef
                'container': this.$el.querySelector('#timer'),
                'width-container': 100,
                'height-container': 100,
                'stroke-width': 10,
                'color-circle': '#F7E967',
                'color-path': '#A9CF54',
                'color-alert': '#FE664E',
                'font-size': 40,
                'font-family': 'Bebas Neue'
            })
        })
    },
    computed: {
        ...mapState(['me', 'midi']),
        ...mapGetters(['getInstrumentChannel'])
    },
    methods: {
        ...mapActions(['fetchMe', 'fetchLevel', 'updateGame', 'finishGameUser', 'generateQuestion', 'storeAnswer', 'setupMidi']),
        playNote (pitch, delay) {
            this.$emit('play-note', pitch, delay)
        },
        addNote (pitch) {
            this.notification = ''
            this.$emit('add-note', pitch)
            this.nAdditions++
        },
        removeNote () {
            if (this.answer.length > 1) {
                this.notification = ''
                this.$emit('remove-note')
                this.nDeletions++
            }
        },
        clearNotes () {
            this.notification = ''
            this.$emit('clear-notes')
        },
        notesChanged (notes) {
            this.answer = notes
        },
        playNotes () {
            if (!this.playing) {
                this.notification = ''
                this.nPlaybacks++
                this.playing = true

                for (let i = 0; i < this.sample.length; i++) {
                    setTimeout(() => this.playNote(this.sample[i], this.notes.delay), i * this.notes.delay)
                }

                setTimeout(() => { this.playing = false }, this.sample.length * this.notes.delay)
            }
        },
        getCurrentTimeInMilliseconds () {
            return new Date().getTime()
        },
        nextQuestion () {
            this.generateQuestion({ game_id: this.game.id, chapter: this.chapter, number: this.number }).then((question) => {
                this.questionId = question.id
                this.sample = question.content.split(',')
                this.$nextTick(() => this.addNote(this.sample[0]))
                this.playNotes()

                this.loading = false
                this.timer.run(this.maxTimePerQuestion, 10000)
                this.startTime = this.getCurrentTimeInMilliseconds()
                this.timeoutId = setTimeout(() => {
                    this.saveAnswer(this.maxTimePerQuestion, false)
                }, this.maxTimePerQuestion)
            })
        },
        checkCorrectness () {
            const timeElapsed = this.getCurrentTimeInMilliseconds() - this.startTime

            this.notification = ''

            if (this.answer.length < this.sample.length) {
                this.notification = 'Vnesli ste premalo not.'
                return
            }

            for (let i = 0; i < this.sample.length; i++) {
                const answerPitch = this.answer[i].pitch
                const correctPitch = this.sample[i]
                if (answerPitch === correctPitch || (answerPitch in this.sharpFlatMap && this.sharpFlatMap[answerPitch] === correctPitch)) {
                    continue
                }
                this.notification = 'Odgovor je napačen.'
                return
            }

            this.saveAnswer(timeElapsed)
        },
        saveAnswer (time, success = true) {
            this.loading = true
            clearTimeout(this.timeoutId)
            this.timer.pause()

            this.storeAnswer({ game_id: this.game.id, user_id: this.me.id, question_id: this.questionId, time: time, n_additions: this.nAdditions, n_deletions: this.nDeletions, n_playbacks: this.nPlaybacks, success: success }).then(() => {
                this.clearNotes()
                this.startTime = 0
                this.nAdditions = 0
                this.nDeletions = 0
                this.nPlaybacks = 0

                if (this.number === this.nQuestions) {
                    this.number = 0
                    this.chapter++
                }

                if (this.chapter > this.nChapters) {
                    this.finishGame()
                } else {
                    this.number++
                    this.nextQuestion()
                }
            })
        },
        finishGame () {
            this.finishGameUser({ gameId: this.game.id, userId: this.me.id }).then(() => {
                this.fetchMe(true).then(() => {
                    // TODO: route to game statistics
                    this.$router.push({ name: 'dashboard' })
                })
            })
        }
    },
    components: {
        stave: Stave,
        keyboard: Keyboard
    }
}
</script>
