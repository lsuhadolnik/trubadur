12<style lang="scss" scoped>
@import '../../../sass/app';

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

.keyboard__key--pressed { fill: $pale-green; }

.keyboard__key-text {
    font-family    : $font-regular;
    pointer-events : none;
    text-anchor    : middle;

    @include breakpoint-phone { display: none; }
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
        <rect class="keyboard__key keyboard__key--white" :class="{ 'keyboard__key--pressed': key.pressed }" :x="(100 / nKeys * index) + '%'" y="0%" :width="(100 / nKeys) + '%'" height="100%" v-for="(key, index) in keys.white" @mousedown="mousedown($event, key)" @mouseup="mouseup($event, key)" @touchstart="mousedown($event, key)" @touchend="mouseup($event, key)"></rect>
        <rect class="keyboard__key keyboard__key--black" :class="{ 'keyboard__key--pressed': key.pressed }" :x="(100 / nKeys * (0.666 + index - 1)) + '%'" y="0%" :width="(100 / nKeys * 0.666) + '%'" height="66.6%" v-for="(key, index) in keys.black" v-if="key.midiPitch !== -1" @mousedown="mousedown($event, key)" @mouseup="mouseup($event, key)" @touchstart="mousedown($event, key)" @touchend="mouseup($event, key)"></rect>
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
                    { pitch: 'A3', midiPitch: 57, code: 81, name: 'Q', pressed: false },
                    { pitch: 'B3', midiPitch: 59, code: 81, name: 'W', pressed: false },
                    { pitch: 'C4', midiPitch: 60, code: 87, name: 'E', pressed: false },
                    { pitch: 'D4', midiPitch: 62, code: 69, name: 'R', pressed: false },
                    { pitch: 'E4', midiPitch: 64, code: 82, name: 'T', pressed: false },
                    { pitch: 'F4', midiPitch: 65, code: 84, name: 'Z', pressed: false },
                    { pitch: 'G4', midiPitch: 67, code: 89, name: 'U', pressed: false },
                    { pitch: 'A4', midiPitch: 69, code: 85, name: 'I', pressed: false },
                    { pitch: 'B4', midiPitch: 71, code: 73, name: 'O', pressed: false },
                    { pitch: 'C5', midiPitch: 72, code: 79, name: 'P', pressed: false },
                    { pitch: 'D5', midiPitch: 74, code: 79, name: 'Š', pressed: false }
                ],
                black: [
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Bb3', midiPitch: 58, code: 49, name: '2', pressed: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Db4', midiPitch: 61, code: 51, name: '4', pressed: false },
                    { pitch: 'Eb4', midiPitch: 63, code: 52, name: '5', pressed: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Gb4', midiPitch: 66, code: 54, name: '7', pressed: false },
                    { pitch: 'Ab4', midiPitch: 68, code: 55, name: '8', pressed: false },
                    { pitch: 'Bb4', midiPitch: 70, code: 56, name: '9', pressed: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Db5', midiPitch: 73, code: 48, name: '?', pressed: false },
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
            helperTextVisible: true
        }
    },
    created () {
        this.$parent.$on('play-note', this.playNote)
    },
    mounted () {
        this.$nextTick(() => {
            window.addEventListener('keydown', this.keydown)
            window.addEventListener('keyup', this.keyup)
        })
    },
    beforeDestroy () {
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
            key.pressed = true
            this.noteOn(key.midiPitch)
        },
        mouseup (event, key) {
            event.preventDefault()
            key.pressed = false
            this.noteOff(key.midiPitch)
            this.$emit('note-played', key.pitch)
        },
        keydown (event) {
            event.preventDefault()
            const key = this.getKeyByCode(event.keyCode)
            if (key && !key.pressed) {
                key.pressed = true
                this.noteOn(key.midiPitch)
            }
        },
        keyup (event) {
            event.preventDefault()
            const key = this.getKeyByCode(event.keyCode)
            if (key && key.pressed) {
                key.pressed = false
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
        }
    }
}
</script>
