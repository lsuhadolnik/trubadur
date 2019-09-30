<style lang="scss" scoped>
@import '../../../sass/variables/index';

.stave__svg {
    width    : 60vw;
    height   : 18vw;
    overflow : visible;

    @include breakpoint-portrait {
        width  : 95vw;
        height : 28.5vw;
    }

    @include breakpoint-large-phone-landscape {
        width  : 50vw;
        height : 15vw;
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

.stave__clef--treble { }
</style>

<template>
    <svg class="stave__svg" viewBox="0 0 100 30" preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" version="1.1">
        <rect class="stave__rect" x="0" y="30%" width="100%" height="40%"></rect>
        <line class="stave__line" x1="0" :y1="(30 + n * 10) + '%'" x2="100%" :y2="(30 + n * 10) + '%'" :key="n" v-for="n in 3"></line>
        <g transform="translate(-9, 3.3) scale(0.32)">
            <path sodipodi:nodetypes="cssssssssscsssssssssc" d="M 39.708934,63.678683 C 39.317094,65.77065 41.499606,70.115061 45.890584,70.256984 C 51.19892,70.428558 54.590321,66.367906 53.010333,59.740875 L 45.086538,23.171517 C 44.143281,18.81826 44.851281,16.457097 45.354941,15.049945 C 46.698676,11.295749 50.055822,9.7473042 50.873134,10.949208 C 51.339763,11.635413 52.468042,14.844006 49.256275,20.590821 C 46.751378,25.072835 35.096985,30.950138 34.2417,41.468011 C 33.501282,50.614249 43.075689,57.369301 51.339266,54.71374 C 56.825686,52.950639 59.653965,44.62402 56.258057,40.328987 C 47.29624,28.994371 32.923702,46.341263 46.846564,51.0935 C 45.332604,49.90238 44.300646,48.980054 44.1085,47.852721 C 42.237755,36.876941 58.741182,39.774741 54.294493,50.18735 C 52.466001,54.469045 45.080341,55.297323 40.874269,51.477433 C 37.350853,48.277521 35.787387,42.113231 39.708327,37.687888 C 45.018831,31.694223 51.288782,26.31366 52.954064,18.108736 C 54.923313,8.4061491 48.493821,0.84188926 44.429027,10.385835 C 43.065093,13.588288 42.557016,16.803074 43.863006,22.963534 L 51.780549,60.311215 C 52.347386,62.985028 51.967911,66.664419 49.472374,68.355474 C 48.236187,69.193154 43.861784,69.769668 42.791575,67.770092" style="fill:black;fill-opacity:1;fill-rule:nonzero;stroke:black;stroke-width:0.1;stroke-linecap:butt;stroke-linejoin:miter;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1"/>
            <path transform="matrix(-1.08512,-2.036848e-2,2.036848e-2,-1.08512,90.68868,135.0572)" d="M 48.24903 64.584198 A 3.439605 3.4987047 0 1 1  41.36982,64.584198 A 3.439605 3.4987047 0 1 1  48.24903 64.584198 z" sodipodi:ry="3.4987047" sodipodi:rx="3.439605" sodipodi:cy="64.584198" sodipodi:cx="44.809425" style="fill:black;fill-opacity:1;stroke:black;stroke-width:0.09213948;stroke-miterlimit:4;stroke-dasharray:none;stroke-opacity:1" sodipodi:type="arc"/>
        </g>
        <note :delay="note.delay" :pitch="note.pitch" :previous-pitch="getPreviousNotePitch(index)" :type="note.type" :count="nNotes" :key="index" v-for="(note, index) in notes"></note>
        <g v-if="helperLinesVisible">
            <line class="stave__line" x1="15%" y1="0" x2="15%" y2="100%"></line>
            <line class="stave__line" :x1="(15 + n * (85 / nNotes)) + '%'" y1="0" :x2="(15 + n * (85 / nNotes)) + '%'" y2="100%" v-for="n in nNotes"></line>
        </g>
    </svg>
</template>

<script>
import { numberProp, stringProp, objectProp } from '../../utils/propValidators'

export default {
    props: {
        nNotes: numberProp(),
        noteType: stringProp(),
        clef: stringProp(),
        sharpFlatMap: objectProp()
    },
    data () {
        return {
            notes: [],
            helperLinesVisible: false
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
            if (this.notes.length < this.nNotes) {
                if (this.notes.length > 0) {
                    const index = this.notes.length
                    const previousPitch = this.notes[index - 1].pitch
                    if ((this.isSharp(previousPitch) && this.isFlat(pitch)) || (this.isFlat(previousPitch) && this.isSharp(pitch))) {
                        pitch = this.sharpFlatMap[pitch]
                    } else if (this.isNatural(previousPitch) && (this.isSharp(pitch) || this.isFlat(pitch))) {
                        const pitches = [pitch, this.sharpFlatMap[pitch]]
                        pitch = pitches[Math.round(Math.random())]
                    }
                }
                this.notes.push({ delay: this.notes.length, pitch: pitch, type: this.noteType })
                this.$emit('notes-changed', this.notes)
            }
        },
        eraseNote () {
            this.notes.pop()
            this.$emit('notes-changed', this.notes)
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
        },
        getPreviousNotePitch (index) {
            return index > 0 ? this.notes[index - 1].pitch : ''
        },
        isSharp (pitch) {
            return pitch.indexOf('#') >= 0
        },
        isFlat (pitch) {
            return pitch.indexOf('b') >= 0
        },
        isNatural (pitch) {
            return !this.isSharp(pitch) && !this.isFlat(pitch)
        }
    }
}
</script>
