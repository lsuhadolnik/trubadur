<style lang="scss" scoped>
@import '../../sass/app';

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
            <text class="keyboard__key-text keyboard__key-text--black" :x="(100 / nKeys * (index === 0 ? 0.166 : index === keys.black.length - 1 ? index - 0.166 : index)) + '%'" y="20%" v-for="(key, index) in keys.black" v-if="keys.midiPitch !== -1">{{ key.name }}</text>
        </g>
        <rect class="keyboard__frame"></rect>
    </svg>
</template>

<script>
export default {
    data () {
        return {
            keys: {
                white: [
                    { pitch: 'B3', midiPitch: 59, code: 81, name: 'Q', pressed: false },
                    { pitch: 'C4', midiPitch: 60, code: 87, name: 'W', pressed: false },
                    { pitch: 'D4', midiPitch: 62, code: 69, name: 'E', pressed: false },
                    { pitch: 'E4', midiPitch: 64, code: 82, name: 'R', pressed: false },
                    { pitch: 'F4', midiPitch: 65, code: 84, name: 'T', pressed: false },
                    { pitch: 'G4', midiPitch: 67, code: 89, name: 'Y', pressed: false },
                    { pitch: 'A4', midiPitch: 69, code: 85, name: 'U', pressed: false },
                    { pitch: 'B4', midiPitch: 71, code: 73, name: 'I', pressed: false },
                    { pitch: 'C5', midiPitch: 72, code: 79, name: 'O', pressed: false }
                ],
                black: [
                    { pitch: 'Bb3', midiPitch: 58, code: 49, name: '1', pressed: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Db4', midiPitch: 61, code: 51, name: '3', pressed: false },
                    { pitch: 'Eb4', midiPitch: 63, code: 52, name: '4', pressed: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Gb4', midiPitch: 66, code: 54, name: '6', pressed: false },
                    { pitch: 'Ab4', midiPitch: 68, code: 55, name: '7', pressed: false },
                    { pitch: 'Bb4', midiPitch: 70, code: 56, name: '8', pressed: false },
                    { midiPitch: -1, code: -1 },
                    { pitch: 'Db5', midiPitch: 73, code: 48, name: '0', pressed: false }
                ]
            },
            helperTextVisible: true
        }
    },
    created () {
        this.$parent.$on('play-note', this.playNote)
    },
    mounted () {
        this.$nextTick(() => {
            window.addEventListener('keydown', (event) => this.keydown(event))
            window.addEventListener('keyup', (event) => this.keyup(event))
        })
    },
    destroyed () {
        window.removeEventListener('keywdown', (event) => this.keydown(event))
        window.removeEventListener('keyup', (event) => this.keyup(event))
    },
    computed: {
        nKeys () {
            return this.keys.white.length
        },
        midi () {
            return this.$store.getters.midi
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
            const key = this.getKeyByCode(event.keyCode)
            if (key && !key.pressed) {
                key.pressed = true
                this.noteOn(key.midiPitch)
            }
        },
        keyup (event) {
            const key = this.getKeyByCode(event.keyCode)
            if (key && key.pressed) {
                key.pressed = false
                this.noteOff(key.midiPitch)
                this.$emit('note-played', key.pitch)
            }
        },
        noteOn (midiPitch) {
            // channel id, note number, velocity, delay
            this.midi.noteOn(0, midiPitch, 32, 0)
        },
        noteOff (midiPitch) {
            // channel id, note number, delay
            this.midi.noteOff(0, midiPitch, 0.5)
        },
        playNote (pitch, delay) {
            const key = this.getKeyByPitch(pitch)
            this.noteOn(key.midiPitch)
            setTimeout(() => this.noteOff(key.midiPitch), delay)
        }
    }
}
</script>
