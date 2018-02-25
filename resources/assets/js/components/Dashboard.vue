<style lang="scss" scoped>
@import '../../sass/app';

.dashboard {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}

.dashboard__loader {
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

.dashboard__command-wrapper {
    padding : 10vh 2.5vw;
    display : flex;
}

.dashboard__command {
    width                 : calc(100vw / 3);
    height                : 50px;
    padding-top           : 5px;
    display               : flex;
    justify-content       : center;
    align-items           : center;
    font-size             : 20px;
    font-family           : $font-title;
    background-color      : $blue;
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
}
</style>

<template>
    <div class="dashboard">
        <img class="dashboard__loader" src="/images/loader.svg" v-show="loading"/>
        <div v-show="!loading">
            <div class="dashboard__command-wrapper">
                <div class="dashboard__command" @click="startIntervalsPractice">Vaja intervalov</div>
            </div>
        </div>
    </div>
</template>

<script>
import Intervals from './Intervals'

export default {
    data () {
        return {
            loading: true,
            intervals: {
                notes: {
                    min: 1,
                    max: 4,
                    type: 'whole',
                    delay: 2000
                }
            }
        }
    },
    created () {
        this.setupMIDIPlugin()
    },
    methods: {
        setupMIDIPlugin () {
            MIDI.loadPlugin({
                soundfontUrl: '/soundfonts/',
                instruments: ['acoustic_grand_piano'],
                onsuccess: () => {
                    MIDI.setVolume(0, 127)
                    MIDI.programChange(0, MIDI.GM.byName['acoustic_grand_piano'].number)
                    this.$store.commit('setMidi', MIDI)
                    this.loading = false
                }
            })
        },
        startIntervalsPractice () {
            this.$router.push({ name: 'intervals', params: { notes: this.intervals.notes } })
        }
    },
    components: {
        intervals: Intervals
    }
}
</script>
