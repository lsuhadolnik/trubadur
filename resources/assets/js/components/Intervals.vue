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
    justify-content : center;
    color           : $black;
    font-family     : $font-regular;
    font-size       : 25px;
    font-weight     : bolder;
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

.intervals__command-wrapper--setting {
    padding        : 20px;
    align-items    : center;
    flex-direction : column;
}

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

.intervals__command--setting {
    margin           : 10px 0;
    background-color : $neon-red;
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
        <div v-show="!loading && setting">
            <div class="intervals__command-wrapper intervals__command-wrapper--setting">
                <div class="intervals__command intervals__command--setting" @click="startGame(5, 4, 4)">1. Letnik (1+3)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(5, 4, 5)">1. Letnik (1+4)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(5, 4, 6)">1. Letnik (1+5)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(12, 4, 4)">2. Letnik (1+3)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(12, 4, 5)">2. Letnik (1+4)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(12, 4, 6)">2. Letnik (1+5)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(12, 4, 7)">2. Letnik (1+6)</div>
                <div class="intervals__command intervals__command--setting" @click="startGame(12, 4, 8)">2. Letnik (1+7)</div>
            </div>
        </div>
        <div v-show="!loading && !setting">
            <div class="intervals__progress-wrapper">
                {{ questionIndex }} / {{ totalQuestions }}
            </div>
            <div class="debug">
                {{ sample.join(',') }} | {{ answer.map(a => a.pitch).join(',') }}
            </div>
            <div class="intervals__stave-keyboard-wrapper">
                <div class="intervals__stave">
                    <stave :min-notes="notes.min" :max-notes="notes.max" :note-type="notes.type" :sharp-flat-map="sharpFlatMap" @notes-changed="notesChanged"></stave>
                </div>
                <div class="intervals__keyboard">
                    <keyboard @note-played="addNote"></keyboard>
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
import { mapActions } from 'vuex'
import Stave from './Stave'
import Keyboard from './Keyboard.vue'

export default {
    data () {
        return {
            loading: true,
            setting: false,
            playing: false,
            notes: {
                levelRange: 5,
                min: 1,
                max: 4,
                type: 'whole',
                delay: 2000
            },
            pitches: ['A#3', 'B3', 'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4', 'C5', 'C#5'],
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
            intervals: {
                0: ['P1'],
                1: ['A1', 'm2'],
                2: ['M2'],
                3: ['m3'],
                4: ['M3'],
                5: ['P4'],
                6: ['A4', 'd5'],
                7: ['P5'],
                8: ['m6'],
                9: ['M6'],
                10: ['m7'],
                11: ['M7'],
                12: ['P8']
            },
            sample: [],
            answer: [],
            questionIndex: 0,
            totalQuestions: 2,
            notification: ''
        }
    },
    created () {
        this.setupMidi().then(() => {
            this.loading = false
            this.setting = true
        })
    },
    methods: {
        ...mapActions(['setupMidi']),
        startGame (levelRange, minNotes, maxNotes) {
            this.setting = false
            this.levelRange = levelRange
            this.notes.min = minNotes
            this.notes.max = maxNotes
            this.generateSample()
        },
        generateSample () {
            this.sample = []

            const nPitches = this.pitches.length
            let pitch = this.pitches[Math.floor(Math.random() * nPitches)]
            this.sample.push(pitch)

            let pitchIndex = 0
            let topRange = 0
            let bottomRange = 0
            let direction = ''
            let range = 0
            let nSemitones = 0
            let intervalIndex = 0

            for (let i = 1; i < this.notes.max; i++) {
                pitchIndex = this.pitches.indexOf(pitch)
                topRange = nPitches - pitchIndex - 1
                bottomRange = pitchIndex
                direction = Math.random() < 0.5 ? 'down' : 'up'
                range = direction === 'down' ? Math.min(this.levelRange, bottomRange) : Math.min(this.levelRange, topRange)
                nSemitones = Math.floor(Math.random() * (range + 1))
                console.log(this.intervals[nSemitones])
                intervalIndex = direction === 'down' ? (pitchIndex - nSemitones) : (pitchIndex + nSemitones)
                pitch = this.pitches[intervalIndex]
                this.sample.push(pitch)
            }

            this.questionIndex++
            console.log(this.questionIndex, this.sample)
            this.clearNotes()
            this.addNote(this.sample[0])
            this.playNotes()
        },
        playNote (pitch, delay) {
            this.$emit('play-note', pitch, delay)
        },
        addNote (pitch) {
            this.notification = ''
            this.$emit('add-note', pitch)
        },
        removeNote () {
            if (this.answer.length > 1) {
                this.$emit('remove-note')
            }
        },
        clearNotes () {
            this.$emit('clear-notes')
        },
        notesChanged (notes) {
            this.answer = notes
        },
        playNotes () {
            if (!this.playing) {
                this.notification = ''
                this.playing = true

                for (let i = 0; i < this.sample.length; i++) {
                    setTimeout(() => this.playNote(this.sample[i], this.notes.delay), i * this.notes.delay)
                }

                setTimeout(() => { this.playing = false }, this.sample.length * this.notes.delay)
            }
        },
        checkCorrectness () {
            this.notification = ''

            if (this.answer.length < this.notes.max) {
                this.notification = 'Vnesli ste premalo not.'
                return
            }
            for (let i = 0; i < this.notes.max; i++) {
                const answerPitch = this.answer[i].pitch
                const correctPitch = this.sample[i]
                if (answerPitch === correctPitch || (answerPitch in this.sharpFlatMap && this.sharpFlatMap[answerPitch] === correctPitch)) {
                    continue
                }
                this.notification = 'Odgovor je napačen.'
                console.log('Wrong answer... ' + this.answer.map(answer => answer.pitch))
                return
            }
            this.clearNotes()
            if (this.questionIndex === this.totalQuestions) {
                this.questionIndex = 0
                this.setting = true
            } else {
                this.generateSample()
            }
        }
    },
    components: {
        stave: Stave,
        keyboard: Keyboard
    }
}
</script>
