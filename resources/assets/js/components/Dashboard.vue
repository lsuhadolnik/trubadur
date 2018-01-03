<style lang="scss" scoped>
@import '../../sass/app';

.dashboard {
    width            : 100%;
    min-height       : calc(100vh - 100px);
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $white;
}

.svg {
    width            : 80vw;
    height           : 40vw;
    background-color : $very-light-gray;
}

.line {
    stroke       : $black;
    stroke-width : 0.2%;
}

.rect {
    stroke       : black;
    stroke-width : 0.2%;
    fill         : none;
}

.clef {

}

.clef--treble {
    x      : 3%;
    y      : 31%;
    height : 40%;
}

.time {

}

// .note {
//     height: 18%;
// }
</style>

<template>
    <div class="dashboard">
        <div id="notes"></div>
        <svg class="svg" viewBox="0 0 100 50" preserveAspectRatio="xMidYMid meet">
            <rect class="rect" x="5%" y="40%" width="90%" height="20%"></rect>
            <line class="line" x1="89.5%" y1="30%" x2="94%" y2="30%"></line>
            <line class="line" x1="79.5%" y1="35%" x2="84%" y2="35%"></line>
            <line class="line" x1="5%" :y1="(40 + n * 5) + '%'" x2="95%" :y2="(40 + n * 5) + '%'" :key="n" v-for="n in 3"></line>
            <line class="line" x1="19.5%" y1="65%" x2="24%" y2="65%"></line>
            <image class="clef clef--treble" xlink:href="/images/clef-treble.svg"/>
            <note :delay="note.delay" :pitch="note.pitch" :type="note.type" :up="note.up" :key="index" v-for="(note, index) in notes"></note>
        </svg>
    </div>
</template>

<script>
import Note from './Note.vue'

export default {
    data () {
        return {
            notes: [
                { delay: 0, pitch: 'c', type: 'whole', up: true },
                { delay: 1, pitch: 'd', type: 'half', up: true },
                { delay: 2, pitch: 'e', type: 'quarter', up: true },
                { delay: 3, pitch: 'f', type: 'eighth', up: true },
                { delay: 4, pitch: 'g', type: 'sixteenth', up: true },
                { delay: 5, pitch: 'a', type: 'thirty-second', up: true },
                { delay: 6, pitch: 'h', type: 'sixty-fourth', up: true }
            ]
        }
    },
    created () { },
    mounted () {
        this.$nextTick(() => {
            var vf = new VF.Factory({ renderer: { elementId: 'notes', width: 500, height: 200 } })

            var score = vf.EasyScore()
            var system = vf.System()

            system.addStave({
                voices: [
                    score.voice(score.notes('C#5/q, B4, A4, G#4', {stem: 'up'})),
                    score.voice(score.notes('C#4/h, C#4', {stem: 'down'}))
                ]
            }).addClef('treble').addTimeSignature('4/4')

            vf.draw()
        })
    },
    computed: { },
    methods: { },
    components: {
        note: Note
    }
}
</script>
