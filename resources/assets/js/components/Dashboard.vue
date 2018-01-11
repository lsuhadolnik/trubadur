<style lang="scss" scoped>
@import '../../sass/app';

.dashboard {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}

.dashboard__stave-keyboard-wrapper {
    padding         : 10vh 2.5vw;
    display         : flex;
    justify-content : center;
    align-items     : center;
    flex-direction  : row;

    @include breakpoint-portrait { flex-direction: column; }
}

.dashboard__stave {
    margin-right: 2.5vw;

    @include-breakpoint-portrait { margin-right: 0; }
}

.dashboard__keyboard {
    margin-left: 2.5vw;

    @include breakpoint-portrait {
        margin-top  : 5vh;
        margin-left : 0;
    }
}

.dashboard__command-wrapper { display: flex; }

.dashboard__command {
    width           : calc(100vw / 3);
    height          : 50px;
    padding-top     : 5px;
    display         : flex;
    justify-content : center;
    align-items     : center;
    font-size       : 20px;
    font-family     : $font-title;
    color           : $black;
    opacity         : 0.8;
    user-select     : none;
    cursor          : pointer;
    transition      : opacity 0.1s linear;

    &:hover { opacity: 1; }
}

.dashboard__command--delete { background-color : $neon-red; }
.dashboard__command--replay { background-color : $blue;     }
.dashboard__command--next   { background-color : $green;    }
</style>

<template>
    <div class="dashboard">
        <div class="dashboard__stave-keyboard-wrapper">
            <div class="dashboard__stave">
                <stave :min-notes="minNotes" :max-notes="maxNotes" :note-type="noteType" @notes-changed="notesChanged"></stave>
            </div>
            <div class="dashboard__keyboard">
                <keyboard @midi-plugin-loaded="MIDIPluginLoaded" @note-played="addNote"></keyboard>
            </div>
        </div>
        <div class="dashboard__command-wrapper">
            <div class="dashboard__command dashboard__command--delete" @click="removeNote">DELETE NOTE</div>
            <div class="dashboard__command dashboard__command--replay" @click="playNotes">REPLAY</div>
            <div class="dashboard__command dashboard__command--next" @click="checkCorrectness">NEXT</div>
        </div>
    </div>
</template>

<script>
import Stave from './Stave'
import Keyboard from './Keyboard.vue'

export default {
    data () {
        return {
            loading: true,
            minNotes: 1,
            maxNotes: 4,
            noteType: 'half',
            sample: null,
            answer: null
        }
    },
    methods: {
        generateSample () {
            const pitches = ['Bb3', 'B3', 'C4', 'Db4', 'D4', 'Eb4', 'E4', 'F4', 'Gb4', 'G4', 'Ab4', 'A4', 'Bb4', 'B4', 'C5', 'Db5']
            this.sample = _.take(_.shuffle(pitches), this.maxNotes)
            this.$emit('clear-notes')
            this.addNote(this.sample[0])
            this.playNotes()
            console.log('New sample generated: ', this.sample)
        },
        MIDIPluginLoaded () {
            this.loading = false
            this.generateSample()
        },
        playNote (pitch) {
            this.$emit('play-note', pitch)
        },
        addNote (pitch) {
            this.$emit('add-note', pitch)
        },
        removeNote () {
            this.$emit('remove-note')
        },
        notesChanged (notes) {
            this.answer = notes
        },
        playNotes () {
            for (let i = 0; i < this.sample.length; i++) {
                setTimeout(() => this.playNote(this.sample[i]), i * 500)
            }
        },
        checkCorrectness () {
            if (this.answer.length < this.maxNotes) {
                return
            }
            for (let i = 0; i < this.maxNotes; i++) {
                if (this.answer[i].pitch !== this.sample[i]) {
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
