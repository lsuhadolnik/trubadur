<template>

    <div>
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" :enabledContexts="['zoomview']" >
                
                <div class="rhythm-game__staff__second-row">
                    <div id="second-row"></div>
                </div>
        </StaffView>

        <SexyButton @click.native="play"  text="Predvajaj"/>
    </div>
</template>

<script>

import StaffView from "./games/rhythm/StaffView.vue"
import SexyButton from "./elements/SexyButton.vue"

import NoteStore from "./games/rhythm/noteStore"
import ExerciseGenerator from './games/rhythm/exerciseGenerator'
import RhythmPlaybackEngine from './games/rhythm/rhythmPlaybackEngine'

import { mapState, mapGetters, mapActions } from 'vuex'

const util = require("./games/rhythm/rhythmUtilities");

export default {

    components: {
        StaffView, SexyButton
    },

    methods: {

        ...mapActions(['fetchMe', 'finishGameUser', 'completeBadges', 'generateQuestion', 'storeAnswer', 'setupMidi']),

        play(){
            this.playback.resume()
        },

        loadQuestion() {

            let out = this;

            out.playback = new RhythmPlaybackEngine(MIDI);

            return this.generateQuestion(
                { 
                    game_id: 283, 
                    number: 3, 
                    chapter: 1
                })
            .then((question) => {
                
                let exercise = question.content;
                    
                out.notes = new NoteStore(
                    exercise.bar,
                    null,
                    out.$refs.staff_view.render
                );

                out.notes.notes = exercise.notes;


                out.notes._call_render();

                out.playback.setBPM(exercise.BPM);
                out.playback.setBar(exercise.bar);

                out.playback.load(exercise.notes);
                console.log(util.generate_playback_durations(exercise.notes));


            });

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
        
        this.$refs.staff_view.init({cursor: {enabled: false}});

        // Init MIDI
        let instruments = [
            {
                channel: 5,
                soundfont: 'percussive_organ',
                colume: 127
            },
            {
                channel: 6,
                soundfont: 'xylophone',
                volume: 200
            }   
        ];

        let out = this;

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


                out.loadQuestion();

                /*

                let v = require('./games/rhythm/vaje.json');
                out.notes.notes = v[0].notes;
                out.notes._call_render();
                out.playback = new RhythmPlaybackEngine(MIDI);
                out.playback.BPM = v[0].BPM;
                out.playback.bar_info = v[0].bar;

                out.playback.setBPM(v[0].BPM);
                out.playback.setBar(v[0].bar);

                out.playback.load(v[0].notes);
                console.log(util.generate_playback_durations(v[0].notes));

                */

        
            }
        });

    }

}

</script>

