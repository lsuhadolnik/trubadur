<template>

    <div>
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" />

        <SexyButton @click.native="play"  text="Predvajaj"/>
    </div>
</template>

<script>

import StaffView from "./games/rhythm/StaffView.vue"
import SexyButton from "./elements/SexyButton.vue"

import NoteStore from "./games/rhythm/noteStore"
import ExerciseGenerator from './games/rhythm/exerciseGenerator'
import RhythmPlaybackEngine from './games/rhythm/rhythmPlaybackEngine'

const util = require("./games/rhythm/rhythmUtilities");

export default {

    components: {
        StaffView, SexyButton
    },

    methods: {

        play(){
            this.playback.resume()
        }

    },

    data() {
        return {

            playback: null,

            notes: null,
            bar: {
                num_beats: 4,
                base_note: 4
            },
            cursor: {
                position: 0,
                x: 0,
                in_tuplet: false,

                cursor_moved: this.cursor_moved,

                selection: null, 
                selectionMode: false,
                selectionSelected: false,

                clearSelection: this.clearSelection,
                toggleSelectionMode: this.toggleSelectionMode,

                editing_tuplet: false,
                editing_tuplet_index: -1
            },
        }
    },
    
    mounted(){
        
        // Init MIDI
        let instruments = [
            {
                channel: 0,
                soundfont: 'percussive_organ',
                colume: 127
            },
            {
                channel: 1,
                soundfont: 'xylophone',
                volume: 200
            }   
        ];

        MIDI.loadPlugin({
            soundfontUrl: '/soundfonts/',
            instruments: instruments.map(e => e.soundfont),
            targetFormat: 'mp3',
            onsuccess: () => {
                for (let i = 0; i < instruments.length; i++) {
                    let instrument = instruments[i];
                    MIDI.setVolume(instrument.channel, instrument.volume);
                    MIDI.programChange(instrument.channel, MIDI.GM.byName[instrument.soundfont].number);
                }

                this.notes = new NoteStore(
                    this.bar,
                    this.cursor,
                    this.$refs.staff_view.render
                );

                

                let v = require('./games/rhythm/vaje.json');
                this.notes.notes = v[0].exercise;
                this.notes._call_render();
                this.playback = new RhythmPlaybackEngine();
                this.playback.BPM = v[0].BPM;
                this.playback.bar_info = v[0].bar;
                this.playback.load(v[0].exercise);
                console.log(util.generate_playback_durations(v[0].exercise));


        
            }
        });

    }

}

</script>

