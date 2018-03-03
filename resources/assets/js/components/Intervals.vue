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

.intervals__stave-keyboard-wrapper {
    padding         : 10vh 2.5vw;
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
     -khtml-user-select   : none;
       -moz-user-select   : none;
        -ms-user-select   : none;
            user-select   : none;
    cursor                : pointer;
    transition            : opacity 0.1s linear;

    @include breakpoint-tablet { opacity : 1; }
    @include breakpoint-phone  { opacity : 1; }

    &:hover { opacity: 1; }
}

.intervals__command--delete { background-color : $neon-red; }
.intervals__command--replay { background-color : $blue;     }
.intervals__command--next   { background-color : $green;    }
</style>

<template>
    <div class="intervals">
        <img class="intervals__loader" src="/images/loader.svg" v-show="loading"/>
        <div v-show="!loading">
            <div class="intervals__stave-keyboard-wrapper">
                <div class="intervals__stave">
                    <stave :min-notes="notes.min" :max-notes="notes.max" :note-type="notes.type" :sharp-flat-map="sharpFlatMap" @notes-changed="notesChanged"></stave>
                </div>
                <div class="intervals__keyboard">
                    <keyboard @note-played="addNote"></keyboard>
                </div>
            </div>
            <div class="intervals__command-wrapper">
                <div class="intervals__command intervals__command--delete" @click="removeNote">DELETE NOTE</div>
                <div class="intervals__command intervals__command--replay" @click="playNotes">REPLAY</div>
                <div class="intervals__command intervals__command--next" @click="checkCorrectness">NEXT</div>
            </div>
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
            notes: {
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
            sample: null,
            answer: null
        }
    },
    created () {
        this.setupMidi().then(() => {
            this.loading = false
            this.generateSample()
        })
    },
    methods: {
        ...mapActions(['setupMidi']),
        generateSample () {
            const levelRange = 5

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
                range = direction === 'down' ? Math.min(levelRange, bottomRange) : Math.min(levelRange, topRange)
                nSemitones = Math.floor(Math.random() * (range + 1))
                console.log(this.intervals[nSemitones])
                intervalIndex = direction === 'down' ? (pitchIndex - nSemitones) : (pitchIndex + nSemitones)
                pitch = this.pitches[intervalIndex]
                this.sample.push(pitch)
            }

            console.log(this.sample)

            this.clearNotes()
            this.addNote(this.sample[0])
            this.playNotes()
        },
        playNote (pitch, delay) {
            this.$emit('play-note', pitch, delay)
        },
        addNote (pitch) {
            this.$emit('add-note', pitch)
        },
        removeNote () {
            this.$emit('remove-note')
        },
        clearNotes () {
            this.$emit('clear-notes')
        },
        notesChanged (notes) {
            this.answer = notes
        },
        playNotes () {
            for (let i = 0; i < this.sample.length; i++) {
                setTimeout(() => this.playNote(this.sample[i], this.notes.delay), i * this.notes.delay)
            }
        },
        checkCorrectness () {
            if (this.answer.length < this.notes.max) {
                console.log('Not enough notes...')
                return
            }
            for (let i = 0; i < this.notes.max; i++) {
                const answerPitch = this.answer[i].pitch
                const correctPitch = this.sample[i]
                if (answerPitch === correctPitch || (answerPitch in this.sharpFlatMap && this.sharpFlatMap[answerPitch] === correctPitch)) {
                    continue
                }
                console.log('Wrong answer... ' + this.answer.map(answer => answer.pitch))
                return
            }
            this.generateSample()
        }
    },
    components: {
        stave: Stave,
        keyboard: Keyboard
    }
}
</script>
