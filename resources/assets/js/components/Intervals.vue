<style lang="scss" scoped>
@import '../../sass/app';

.intervals {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
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
        <div class="intervals__stave-keyboard-wrapper">
            <div class="intervals__stave">
                <stave :min-notes="notes.min" :max-notes="notes.max" :note-type="notes.type" @notes-changed="notesChanged"></stave>
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
</template>

<script>
import Stave from './Stave'
import Keyboard from './Keyboard.vue'
import { objectProp } from '../utils/propValidators'

export default {
    props: {
        notes: objectProp()
    },
    data () {
        return {
            sample: null,
            answer: null
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.generateSample()
        })
    },
    methods: {
        generateSample () {
            const pitches = ['Bb3', 'B3', 'C4', 'Db4', 'D4', 'Eb4', 'E4', 'F4', 'Gb4', 'G4', 'Ab4', 'A4', 'Bb4', 'B4', 'C5', 'Db5']
            this.sample = _.take(_.shuffle(pitches), this.notes.max)
            this.clearNotes()
            this.addNote(this.sample[0])
            $(this.$el.querySelector('.intervals__command--replay')).click()
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
                if (this.answer[i].pitch !== this.sample[i]) {
                    console.log('Wrong answer...')
                    return
                }
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
