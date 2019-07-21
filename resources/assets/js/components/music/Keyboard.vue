<style lang="scss" scoped>
@import '../../../sass/variables/index';

.keyboard {
    width  : 45vw;
    height : 13.5vw;

    @include breakpoint-portrait {
        width  : 95vw;
        height : 28.5vw;
    }
}

.keyboard__key { cursor: pointer; }

.keyboard__key--white {
    fill         : $white;
    stroke       : $black;
    stroke-width : 1px;
}

.keyboard__key--black { fill: $black; }

.keyboard__key--correct { fill: $pale-green; }

.keyboard__key--incorrect { fill: $cabaret; }

.keyboard__key-text {
    font-family    : $font-regular;
    pointer-events : none;
    text-anchor    : middle;

    @include breakpoint-tablet      { display: none; }
    @include breakpoint-large-phone { display: none; }
    @include breakpoint-phone       { display: none; }
    @include breakpoint-small-phone { display: none; }
}

.keyboard__key-text--white {
    fill      : $black;
    font-size : 4px;
}

.keyboard__key-text--black {
    fill      : $white;
    font-size : 3px;
}

.keyboard__frame {
    width        : 100px;
    height       : 100px;
    fill         : none;
    stroke       : $black;
    stroke-width : 1px;
}
</style>

<template>
    <svg class="keyboard" viewBox="0 0 100 30" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" version="1.1">
        <rect class="keyboard__key keyboard__key--white" :class="{ 'keyboard__key--correct': key.correct, 'keyboard__key--incorrect': key.incorrect }" :x="(100 / nKeys * index) + '%'" y="0%" :width="(100 / nKeys) + '%'" height="100%" v-for="(key, index) in keys.white" @mousedown="mousedown($event, key)" @mouseup="mouseup($event, key)" @touchstart="mousedown($event, key)" @touchend="mouseup($event, key)"></rect>
        <rect class="keyboard__key keyboard__key--black" :class="{ 'keyboard__key--correct': key.correct, 'keyboard__key--incorrect': key.incorrect }" :x="(100 / nKeys * (0.666 + index - 1)) + '%'" y="0%" :width="(100 / nKeys * 0.666) + '%'" height="66.6%" v-for="(key, index) in keys.black" v-if="key.midiPitch !== -1" @mousedown="mousedown($event, key)" @mouseup="mouseup($event, key)" @touchstart="mousedown($event, key)" @touchend="mouseup($event, key)"></rect>
        <g v-show="helperTextVisible">
            <text class="keyboard__key-text keyboard__key-text--white" :x="(100 / nKeys * (0.5 + index)) + '%'" y="90%" v-for="(key, index) in keys.white">{{ key.name }}</text>
            <text class="keyboard__key-text keyboard__key-text--black" :x="(100 / nKeys * index) + '%'" y="20%" v-for="(key, index) in keys.black" v-if="keys.midiPitch !== -1">{{ key.name }}</text>
        </g>
        <rect class="keyboard__frame"></rect>
    </svg>
</template>

<script>
import { numberProp, objectNullProp } from '../../utils/propValidators'

export default {
    props: {
        channel: numberProp(),
        midi: objectNullProp()
    },
    data () {
        return {
            keys: {
                white: [
                    { pitch: 'A3', midiPitch: 57, code: 81, name: 'Q', correct: false, incorrect: false },
                    { pitch: 'B3', midiPitch: 59, code: 87, name: 'W', correct: false, incorrect: false },
                    { pitch: 'C4', midiPitch: 60, code: 69, name: 'E', correct: false, incorrect: false },
                    { pitch: 'D4', midiPitch: 62, code: 82, name: 'R', correct: false, incorrect: false },
                    { pitch: 'E4', midiPitch: 64, code: 84, name: 'T', correct: false, incorrect: false },
                    { pitch: 'F4', midiPitch: 65, code: 89, name: 'Z', correct: false, incorrect: false },
                    { pitch: 'G4', midiPitch: 67, code: 85, name: 'U', correct: false, incorrect: false },
                    { pitch: 'A4', midiPitch: 69, code: 73, name: 'I', correct: false, incorrect: false },
                    { pitch: 'B4', midiPitch: 71, code: 79, name: 'O', correct: false, incorrect: false },
                    { pitch: 'C5', midiPitch: 72, code: 80, name: 'P', correct: false, incorrect: false },
                    { pitch: 'D5', midiPitch: 74, code: 219, name: 'Š', correct: false, incorrect: false }
                ],
                black: [
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Bb3', midiPitch: 58, code: 50, name: '2', correct: false, incorrect: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Db4', midiPitch: 61, code: 52, name: '4', correct: false, incorrect: false },
                    { pitch: 'Eb4', midiPitch: 63, code: 53, name: '5', correct: false, incorrect: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Gb4', midiPitch: 66, code: 55, name: '7', correct: false, incorrect: false },
                    { pitch: 'Ab4', midiPitch: 68, code: 56, name: '8', correct: false, incorrect: false },
                    { pitch: 'Bb4', midiPitch: 70, code: 57, name: '9', correct: false, incorrect: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Db5', midiPitch: 73, code: 189, name: '?', correct: false, incorrect: false },
                    { midiPitch: -1, code: -1 }
                ]
            },
            pitchMap: {
                'A#3': 'Bb3',
                'B#3': 'C4',
                'Cb4': 'B3',
                'C#4': 'Db4',
                'D#4': 'Eb4',
                'E#4': 'F4',
                'Fb4': 'E4',
                'F#4': 'Gb4',
                'G#4': 'Ab4',
                'A#4': 'Bb4',
                'B#4': 'C5',
                'Cb5': 'B4',
                'C#5': 'Db5'
            },
            helperTextVisible: false
        }
    },
    created () {
        this.$parent.$on('play-note', this.playNote)
        this.$parent.$on('color-key', this.colorKey)
    },
    mounted () {
        this.$nextTick(() => {
            window.addEventListener('keydown', this.keydown)
            window.addEventListener('keyup', this.keyup)
        })
    },
    beforeDestroy () {
        this.$parent.$off('play-note', this.playNote)
        this.$parent.$off('color-key', this.colorKey)
        window.removeEventListener('keydown', this.keydown)
        window.removeEventListener('keyup', this.keyup)
    },
    computed: {
        nKeys () {
            return this.keys.white.length
        }
    },
    methods: {
        getKeyByCode (keyCode) {
            const keys = [
                ...this.keys.white.filter(key => key.code === keyCode),
                ...this.keys.black.filter(key => key.code === keyCode)
            ]
            return keys.length > 0 ? keys[0] : null
        },
        getKeyByPitch (pitch) {
            pitch = pitch in this.pitchMap ? this.pitchMap[pitch] : pitch
            const keys = [
                ...this.keys.white.filter(key => key.pitch === pitch),
                ...this.keys.black.filter(key => key.pitch === pitch)
            ]
            return keys.length > 0 ? keys[0] : null
        },
        mousedown (event, key) {
            event.preventDefault()
            this.$emit('key-pressed', key.pitch)
        },
        mouseup (event, key) {
            event.preventDefault()
            key.correct = false
            key.incorrect = false
            this.noteOff(key.midiPitch)
            this.$emit('note-played', key.pitch)
        },
        keydown (event) {
            event.preventDefault()
            const key = this.getKeyByCode(event.keyCode)
            if (key && !(key.correct || key.incorrect)) {
                this.$emit('key-pressed', key.pitch)
            }
        },
        keyup (event) {
            event.preventDefault()
            const key = this.getKeyByCode(event.keyCode)
            if (key && (key.correct || key.incorrect)) {
                key.correct = false
                key.incorrect = false
                this.noteOff(key.midiPitch)
                this.$emit('note-played', key.pitch)
            }
        },
        noteOn (midiPitch) {
            // channel id, note number, velocity, delay
            this.midi.noteOn(this.channel, midiPitch, 32, 0)
        },
        noteOff (midiPitch) {
            // channel id, note number, delay
            this.midi.noteOff(this.channel, midiPitch, 0.2)
        },
        playNote (pitch, delay) {
            const key = this.getKeyByPitch(pitch)
            this.noteOn(key.midiPitch)
            setTimeout(() => this.noteOff(key.midiPitch), 500)
        },
        colorKey ({ pitch, correct }) {
            const key = this.getKeyByPitch(pitch)
            const which = correct ? 'correct' : 'incorrect'
            key[which] = true
            this.noteOn(key.midiPitch)
        }
    }
}
</script>
