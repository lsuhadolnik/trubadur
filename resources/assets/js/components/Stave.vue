<style lang="scss" scoped>
@import '../../sass/app';

.stave__svg {
    width  : 45vw;
    height : 13.5vw;

    @include breakpoint-portrait {
        width  : 95vw;
        height : 28.5vw;
    }
}

.stave__line {
    stroke       : $black;
    stroke-width : 0.2%;
}

.stave__rect {
    stroke       : $black;
    stroke-width : 0.2%;
    fill         : none;
}

.stave__clef--treble {
    x      : -1%;
    y      : 12%;
    height : 79%;
}
</style>

<template>
    <svg class="stave__svg" viewBox="0 0 100 30" preserveAspectRatio="xMidYMid meet">
        <rect class="stave__rect" x="0" y="30%" width="100%" height="40%"></rect>
        <line class="stave__line" x1="0" :y1="(30 + n * 10) + '%'" x2="100%" :y2="(30 + n * 10) + '%'" :key="n" v-for="n in 3"></line>
        <image class="stave__clef--treble" xlink:href="/images/clef-treble.svg"/>
        <note :delay="note.delay" :pitch="note.pitch" :type="note.type" :count="maxNotes" :key="index" v-for="(note, index) in notes"></note>
        <g v-if="helperLines">
            <line class="stave__line" x1="15%" y1="0" x2="15%" y2="100%"></line>
            <line class="stave__line" :x1="(15 + n * (85 / maxNotes)) + '%'" y1="0" :x2="(15 + n * (85 / maxNotes)) + '%'" y2="100%" v-for="n in maxNotes"></line>
        </g>
    </svg>
</template>

<script>
import Note from './Note.vue'
import { numberProp, stringProp } from '../utils/propValidators'

export default {
    props: {
        minNotes: numberProp(),
        maxNotes: numberProp(),
        noteType: stringProp()
    },
    data () {
        return {
            notes: [],
            helperLines: false
        }
    },
    created () {
        this.$parent.$on('add-note', this.drawNote)
        this.$parent.$on('remove-note', this.eraseNote)
        this.$parent.$on('clear-notes', this.eraseAllNotes)
    },
    mounted () {
        this.$nextTick(() => {
            window.addEventListener('keydown', (event) => this.keydown(event))
        })
    },
    destroyed () {
        window.removeEventListener('keydown', (event) => this.keydown(event))
    },
    methods: {
        drawNote (pitch) {
            if (this.notes.length < this.maxNotes) {
                this.notes.push({ delay: this.notes.length, pitch: pitch, type: this.noteType })
                this.$emit('notes-changed', this.notes)
            }
        },
        eraseNote () {
            if (this.notes.length > this.minNotes) {
                this.notes.pop()
                this.$emit('notes-changed', this.notes)
            }
        },
        eraseAllNotes () {
            this.notes = []
            this.$emit('notes-changed', this.notes)
        },
        keydown (event) {
            switch (event.keyCode) {
                // backspace & delete
                case 8:
                case 46:
                    this.eraseNote()
                    break
            }
        }
    },
    components: {
        note: Note
    }
}
</script>
