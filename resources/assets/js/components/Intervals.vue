<style lang="scss" scoped>
@import '../../sass/variables/index';

.intervals {
    width          : 100%;
    padding-bottom : 20px;
}

.intervals__instructions {
    padding        : 20px 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.intervals__instructions-list-item { padding: 8px 20px 8px 3px; }

.intervals__progress-wrapper {
    padding         : 20px 2.5vw 0 2.5vw;
    display         : flex;
    justify-content : space-between;
    align-items     : center;

    @include breakpoint-portrait {
        padding-top     : 20px;
        justify-content : space-evenly;
        flex-direction  : column;
    }
}

.intervals__progress-chapters {
    width        : 50%;
    margin-right : 20px;
    display      : flex;

    @include breakpoint-portrait {
        width        : 100%;
        margin-right : 0;
        padding      : 0 2.5vw 15px 2.5vw;
    }
}

.intervals__progress-chapter {
    position         : relative;
    width            : calc((100vw - 4vw) / 3);
    height           : 20px;
    background-color : $moss-green;

    &:first-child {
        margin-right  : 2vw;
        border-radius : 10px 0 0 10px;

        .intervals__progress-question { border-radius: 10px 0 0 10px; }
    }

    &:last-child {
        margin-left   : 2vw;
        border-radius : 0 10px 10px 0;
    }

    @include breakpoint-portrait { width: calc((100% - 4vw) / 3); }
}

.intervals__progress-question {
    position         : absolute;
    height           : 20px;
    background-color : $fern;
}

.intervals__stave-keyboard-wrapper {
    margin-top      : 10px;
    padding         : 0 2.5vw;
    display         : flex;
    justify-content : center;
    align-items     : center;
    flex-direction  : row;

    @include breakpoint-portrait {
        margin-top     : 0;
        flex-direction : column;
    }
}

.intervals__stave {
    margin-right: 2.5vw;

    @include breakpoint-portrait { margin-right: 0; }
}

.intervals__keyboard {
    margin-left: 2.5vw;

    @include breakpoint-portrait {
        margin-top  : 10px;
        margin-left : 0;
    }
}

.intervals__commands {
    margin-top      : 10px;
    display         : flex;
    justify-content : space-evenly;

    @include breakpoint-portrait { margin-top: 20px; }
}

.intervals__notification-label {
    color           : $cabaret;
    font-size       : 18px;
}

.intervals__notification-label--portrait {
    margin-top      : 20px;
    display         : flex;
    justify-content : center;

    @include breakpoint-landscape { display: none; }
}

.intervals__notification-label--landscape {
    width      : 150px;
    text-align : center;

    @include breakpoint-portrait { display: none; }
}
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.intervals__instructions .button {
    margin: 30px 0 40px 0;

    .button__full { background-color: $sea-green; }
}
.intervals__command--delete .button {
    width  : 100px !important;
    height : 60px !important;

    @include breakpoint-small-phone-portrait {
        width  : 100px !important;
        height : 50px !important;

        .button__hollow {
            padding   : 2px;
            font-size : 13px;
        }
    }

    .button__full { background-color: $jaffa; }
}
.intervals__command--replay .button {
    width  : 115px !important;
    height : 60px !important;

    @include breakpoint-small-phone-portrait {
        width  : 100px !important;
        height : 50px !important;

        .button__hollow {
            padding   : 2px;
            font-size : 13px;
        }
    }

    .button__full{ background-color: $jaffa; }
}
.intervals__command--next .button {
    width  : 100px !important;
    height : 60px !important;

    @include breakpoint-small-phone-portrait {
        width  : 90px !important;
        height : 50px !important;

        .button__hollow {
            padding   : 2px;
            font-size : 13px;
        }
    }

    .button__full { background-color: $fern; }
}
</style>

<template>
    <div class="intervals">
        <loader v-show="loading"></loader>
        <div class="intervals__instructions" v-show="!loading && instructing">
            <element-button text="začni" @click.native="startGame()"></element-button>
            <ul class="intervals__instructions-list">
                <li class="intervals__instructions-list-item">Preizkusil se boš v igri ugotavljanja intervalov</li>
                <li class="intervals__instructions-list-item">Igra je razdeljena v 3 poglavja, vsako izmed njih ima 8 vprašanj</li>
                <li class="intervals__instructions-list-item">Za odgovor na posamezno vprašanje imaš na voljo natanko 120 sekund</li>
                <li class="intervals__instructions-list-item">Za vnos not na notno črtovje uporabi klaviaturo</li>
                <li class="intervals__instructions-list-item">Na voljo imaš še ukaz za brisanje not, ponovno predvajanje tonov in premik na naslednje vprašanje</li>
                <li class="intervals__instructions-list-item" v-show="!isPractice">Uspešnost reševanja nalog bo vplivala na tvoj položaj na lestvici</li>
                <li class="intervals__instructions-list-item">Na koncu igre si lahko ogledaš statistiko</li>
            </ul>
        </div>
        <div v-show="!loading && !instructing">
            <div class="intervals__progress-wrapper">
                <div class="intervals__progress-chapters">
                    <div class="intervals__progress-chapter" v-for="n in nChapters">
                        <div class="intervals__progress-question" :style="{ 'width': (chapter > n ? 100 : ((number - 1) * 100 / nQuestions)) + '%' }" v-show="chapter >= n"></div>
                    </div>
                </div>
                <svg id="timer"></svg>
                <label class="intervals__notification-label intervals__notification-label--landscape">{{ notification }}</label>
            </div>
            <label style="padding-left: 10px" v-show="debug">{{ sample.join(',') }} | {{ answer.map(a => a.pitch).join(',') }}</label>
            <div class="intervals__stave-keyboard-wrapper">
                <div class="intervals__stave">
                    <stave :n-notes="sample.length" :note-type="notes.type" :clef="notes.clef" :sharp-flat-map="sharpFlatMap" @notes-changed="notesChanged"></stave>
                </div>
                <div class="intervals__keyboard">
                    <keyboard :channel="channel" :midi="midi" @note-played="addNote"></keyboard>
                </div>
            </div>
            <div class="intervals__commands">
                <div class="intervals__command--delete">
                    <element-button text="izbriši noto" :disable="answer.length <= 1" @click.native="removeNote()"></element-button>
                </div>
                <div class="intervals__command--replay">
                    <element-button text="predvajaj" :disable="playing" @click.native="playNotes()"></element-button>
                </div>
                <div class="intervals__command--next">
                    <element-button text="naprej" :disable="playing" @click.native="checkCorrectness()"></element-button>
                </div>
            </div>
            <label class="intervals__notification-label intervals__notification-label--portrait">{{ notification }}</label>
        </div>
    </div>
</template>

<script>
import { mapState, mapGetters, mapActions } from 'vuex'

export default {
    props: ['game'],
    data () {
        return {
            loading: true,
            instructing: false,
            playing: false,
            debug: false,
            notes: {
                type: 'whole',
                delay: 0,
                min: 0,
                max: 0,
                clef: ''
            },
            channel: 0,
            sharpFlatMap: {
                'A#3': 'Bb3',
                'Bb3': 'A#3',
                'C#4': 'Db4',
                'Db4': 'C#4',
                'D#4': 'Eb4',
                'Eb4': 'D#4',
                'F#4': 'Gb4',
                'Gb4': 'F#4',
                'G#4': 'Ab4',
                'Ab4': 'G#4',
                'A#4': 'Bb4',
                'Bb4': 'A#4',
                'C#5': 'Db5',
                'Db5': 'C#5'
            },
            nChapters: 3,
            nQuestions: 8,
            maxTimePerQuestion: 120000,
            timer: null,
            chapter: 1,
            number: 1,
            questionId: 0,
            sample: [],
            answer: [],
            startTime: 0,
            timeoutId: 0,
            nAdditions: 0,
            nDeletions: 0,
            nPlaybacks: 0,
            notification: ''
        }
    },
    created () {
        if (!this.game) {
            this.$router.push({ name: 'dashboard' })
        } else {
            this.fetchMe().then(() => {
                this.notes.delay = this.me.note_playback_delay
                this.notes.clef = this.me.clef
                this.channel = this.getInstrumentChannel(this.me.instrument)

                this.setupMidi().then(() => {
                    this.generateQuestion({ game_id: this.game.id, chapter: this.chapter, number: this.number }).then((question) => {
                        this.questionId = question.id
                        this.sample = question.content.split(',')
                        this.$nextTick(() => this.addNote(this.sample[0]))
                        this.loading = false
                        this.instructing = true
                    })
                })
            })
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.timer = new TimerProgress({ // eslint-disable-line no-undef
                'container': this.$el.querySelector('#timer'),
                'width-container': 100,
                'height-container': 100,
                'stroke-width': 10,
                'color-circle': '#F8A16E',
                'color-path': '#EB7D3D',
                'color-alert': '#D2495F',
                'font-size': 25,
                'font-family': 'GothamRounded-Bold'
            })
        })
    },
    beforeDestroy () {
        clearTimeout(this.timeoutId)
    },
    computed: {
        ...mapState(['me', 'midi']),
        ...mapGetters(['getInstrumentChannel']),
        isPractice () {
            return this.game ? (this.game.mode === 'practice') : false
        }
    },
    methods: {
        ...mapActions(['fetchMe', 'finishGameUser', 'generateQuestion', 'storeAnswer', 'setupMidi']),
        playNote (pitch, delay) {
            this.$emit('play-note', pitch, delay)
        },
        addNote (pitch) {
            this.notification = ''
            this.$emit('add-note', pitch)
            this.nAdditions++
        },
        removeNote () {
            if (this.answer.length > 1) {
                this.notification = ''
                this.$emit('remove-note')
                this.nDeletions++
            }
        },
        clearNotes () {
            this.notification = ''
            this.$emit('clear-notes')
        },
        notesChanged (notes) {
            this.answer = notes
        },
        playNotes () {
            if (!this.playing) {
                this.notification = ''
                this.nPlaybacks++
                this.playing = true

                for (let i = 0; i < this.sample.length; i++) {
                    setTimeout(() => this.playNote(this.sample[i], this.notes.delay), i * this.notes.delay)
                }

                setTimeout(() => { this.playing = false }, this.sample.length * this.notes.delay)
            }
        },
        getCurrentTimeInMilliseconds () {
            return new Date().getTime()
        },
        startGame () {
            this.instructing = false
            this.playNotes()
            this.timer.run(this.maxTimePerQuestion, 10000)
            this.startTime = this.getCurrentTimeInMilliseconds()
            this.timeoutId = setTimeout(() => {
                this.saveAnswer(this.maxTimePerQuestion, false)
            }, this.maxTimePerQuestion)
        },
        checkCorrectness () {
            const timeElapsed = this.getCurrentTimeInMilliseconds() - this.startTime

            this.notification = ''

            if (this.answer.length < this.sample.length) {
                this.notification = 'Vnesli ste premalo not.'
                return
            }

            for (let i = 0; i < this.sample.length; i++) {
                const answerPitch = this.answer[i].pitch
                const correctPitch = this.sample[i]
                if (answerPitch === correctPitch || (answerPitch in this.sharpFlatMap && this.sharpFlatMap[answerPitch] === correctPitch)) {
                    continue
                }
                this.notification = 'Odgovor je napačen.'
                return
            }

            this.saveAnswer(timeElapsed)
        },
        saveAnswer (time, success = true) {
            this.loading = true
            clearTimeout(this.timeoutId)
            this.timer.pause()

            this.storeAnswer({ game_id: this.game.id, user_id: this.me.id, question_id: this.questionId, time: time, n_additions: this.nAdditions, n_deletions: this.nDeletions, n_playbacks: this.nPlaybacks, success: success }).then(() => {
                this.clearNotes()
                this.startTime = 0
                this.nAdditions = 0
                this.nDeletions = 0
                this.nPlaybacks = 0

                if (this.number === this.nQuestions) {
                    this.number = 0
                    this.chapter++
                }

                if (this.chapter > this.nChapters) {
                    this.finishGame()
                } else {
                    this.number++
                    this.nextQuestion()
                }
            })
        },
        nextQuestion () {
            this.generateQuestion({ game_id: this.game.id, chapter: this.chapter, number: this.number }).then((question) => {
                this.questionId = question.id
                this.sample = question.content.split(',')
                this.$nextTick(() => this.addNote(this.sample[0]))
                this.playNotes()

                this.loading = false
                this.timer.run(this.maxTimePerQuestion, 10000)
                this.startTime = this.getCurrentTimeInMilliseconds()
                this.timeoutId = setTimeout(() => {
                    this.saveAnswer(this.maxTimePerQuestion, false)
                }, this.maxTimePerQuestion)
            })
        },
        finishGame () {
            this.finishGameUser({ gameId: this.game.id, userId: this.me.id }).then(() => {
                this.$router.push({ name: 'gameStatistics', params: { id: this.game.id } })
            })
        }
    }
}
</script>
