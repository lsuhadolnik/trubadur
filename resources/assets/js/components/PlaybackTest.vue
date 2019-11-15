<template>

    <div>
        <StaffView ref="staff_view" :bar="bar" :cursor="cursor" :enabledContexts="['zoomview']" >
                
                <div class="rhythm-game__staff__second-row">
                    <div id="second-row"></div>
                </div>
        </StaffView>

        <SexyButton @click.native="play" :cols="3" color="green" text="Predvajaj"/>
        <SexyButton @click.native="stop" :cols="3" color="cabaret" text="Ustavi"/>
        <input type="range" v-model="offsetConstant" step="0.001" min="0.195" max="0.5" width="200"/> {{offsetConstant}}
    
    
        <audio controls ref="rhythmAudio">
            <source :src="audioSource" type="audio/mpeg">
        </audio>

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

            let durs = util.generate_playback_durations(this.notes.notes);
            let start = 0;
            for(let i = 0; i < durs.length; i++){

                let d = durs[i];
                let actualDuration = Math.abs(d.valueOf()) * this.bar.base_note * (60 / this.BPM);

                if(d > 0){

                    this.MIDISources.push(MIDI.noteOn(5, 65, 127, start, actualDuration));
                    MIDI.noteOff(5, 65, start + actualDuration - 0.1);

                }

                start += actualDuration;
            }

        },

        stop() {
            this.MIDISources.forEach(s => {
                s.stop();
            })

            this.MIDISources = [];
        },

        playNote(d) {

            actualDuration = dur.valueOf() * this.bar.base_note * (60 / this.BPM);

            // Tole sem naredil samo zato, da prvo noto pri count-inu drugače zapoje
            // Lahko bi kdaj v prihodnosti dodali melodično-ritmični narek...
            // S tem Math.min sem hotel tudi povedati, da naj se ustavi na zadnjem pitchu, ki je podan.
            let sPitch = this.pitch[Math.min(this.pitch.length - 1, this.currentNoteID - 1)];

            // Zaigraj, ustavi se samodejno.
            this.midi.noteOn(this.channel, sPitch, this.intensity, 0);
            this.midi.noteOff(this.channel, sPitch, actualDuration);

        },

        loadQuestion() {

            let out = this;

            out.playback = new RhythmPlaybackEngine(MIDI);

            return this.generateQuestion(
                { 
                    game_id: 163, 
                    number: 1, 
                    chapter: 1
                })
            .then((question) => {

                let exercise = question.content;
                    
                let exid = exercise.id;
                out.audioSource = "/api/sound/"+exid;
                
                out.$refs.rhythmAudio.pause();
                out.$refs.rhythmAudio.load();
                out.$refs.rhythmAudio.addEventListener('canplaythrough', (e) =>{

                    out.$refs.rhythmAudio.play();

                }, false);

                out.$set(out, 'bar', exercise.timeSignature);

                out.notes = new NoteStore(
                    exercise.bar,
                    null,
                    out.$refs.staff_view.render
                );

                out.notes.notes = exercise.notes;
                out.BPM = exercise.BPM;

                out.$nextTick(() => {
                    out.notes._call_render();
                })
                

                out.playback.setBPM(exercise.BPM);
                out.playback.setBar(exercise.bar);

                out.playback.load(exercise.notes);


            });

        }

    },

    data() {
        return {

            audioSource: "",

            offsetConstant: 0.195,

            MIDISources: [],

            playback: null,

            notes: null,
            bar: {
                num_beats: 4,
                base_note: 4
            },
            BPM: 60,
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
                soundfont: 'trumpet',
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

