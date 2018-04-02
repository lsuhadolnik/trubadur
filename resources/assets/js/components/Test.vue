<style lang="scss" scoped>
@import '../../sass/variables/index';

.test {
    width            : 100%;
    height           : calc(100% - 50px);
    padding          : 10px;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $aero-blue;
}

.test__title {
    font-family : $font-regular;
    font-size   : 30px;
    font-weight : bold;
}

.test__loader {
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

.test__command-wrapper {
    padding        : 20px 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.test__command {
    width                 : calc(100vw / 3);
    height                : 50px;
    margin                : 10px 0;
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
    <div class="test">
        <div class="test__title">{{ title | uppercase }}</div>
        <img class="test__loader" src="/images/loader.svg" v-show="loading"/>
        <div class="test__command-wrapper" v-show="!loading">
            <div class="test__command" @click="startPlaying(melodies.scale)" v-if="!playing">Play</div>
            <div class="test__command" @click="stopPlaying()" v-if="playing">Stop</div>
            <div>{{ this.playing ? 'Playing' : 'Not playing' }}</div>
            <div v-for="pitch in pitches">
                <div class="test__command" @click="play(pitch)">Play {{ pitch }}</div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            title: 'test',
            loading: false,
            pitches: ['A3', 'B3', 'C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4', 'C5', 'Bb3', 'Db4', 'Eb4', 'Gb4', 'Ab4', 'Bb4', 'Db5'],
            sounds: {},
            audios: {},
            maxAudios: 50,
            delay: 1000,
            playing: false,
            playingTimeoutId: 0,
            timeoutIds: [],
            melodies: {
                jingleBells: ['E4', 'E4', 'E4', 'E4', 'E4', 'E4', 'E4', 'G4', 'C4', 'D4', 'E4', 'F4', 'F4', 'F4', 'F4', 'F4', 'E4', 'E4', 'E4', 'E4', 'D4', 'D4', 'E4', 'D4', 'G4', 'E4', 'E4', 'E4', 'E4', 'E4', 'E4', 'E4', 'G4', 'C4', 'D4', 'E4', 'F4', 'F4', 'F4', 'F4', 'F4', 'E4', 'E4', 'E4', 'G4', 'G4', 'F4', 'D4', 'C4'],
                scale: ['C4', 'D4', 'E4', 'F4', 'G4', 'A4', 'B4', 'C5', 'C5', 'B4', 'A4', 'G4', 'F4', 'E4', 'D4', 'C4']
            },
            store: {
                dbConnection: null,
                dbName: 'cache',
                tableName: 'sounds'
            },
            browser: ''
        }
    },
    created () {
        // this.loadSounds('piano')
    },
    destroyed () {
        this.stopPlaying()
    },
    methods: {
        determineBrowser () {
            // Opera 8.0+
            const isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0 // eslint-disable-line no-undef
            if (isOpera) {
                this.browser = 'opera'
            }

            // Firefox 1.0+
            const isFirefox = typeof InstallTrigger !== 'undefined'
            if (isFirefox) {
                this.browser = 'firefox'
            }

            // Safari 3.0+ '[object HTMLElementConstructor]'
            const isSafari = /constructor/i.test(window.HTMLElement) || (function (p) { return p.toString() === '[object SafariRemoteNotification]' })(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification)) // eslint-disable-line no-undef
            if (isSafari) {
                this.browser = 'safari'
            }

            // Internet Explorer 6-11
            const isIE = false || !!document.documentMode
            if (isIE) {
                this.browser = 'ie'
            }

            // Edge 20+
            const isEdge = !isIE && !!window.StyleMedia
            if (isEdge) {
                this.browser = 'edge'
            }

            // Chrome 1+
            const isChrome = !!window.chrome && !!window.chrome.webstore
            if (isChrome) {
                this.browser = 'chrome'
            }
        },
        loadSounds (instrument) {
            this.store.dbConnection = new JsStore.Instance() // eslint-disable-line no-undef

            JsStore.isDbExist(this.store.dbName, // eslint-disable-line no-undef
                (exists) => {
                    // database already exists
                    if (exists) {
                        this.store.dbConnection.openDb(this.store.dbName)
                        this.store.dbConnection.select({ From: this.store.tableName, Where: { instrument: instrument } },
                            (data) => {
                                // data was already present in the store
                                if (data.length > 0) {
                                    for (let sound of data) {
                                        this.sounds[sound.pitch] = sound.data
                                    }
                                    this.initializeAudio()
                                    console.log(`${data.length} records loaded from the '${this.store.tableName}' table.`)
                                // otherwise, load sounds from the server and store them into the store
                                } else {
                                    this.fetchSounds(instrument)
                                }
                            },
                            (error) => console.log(`An error occurred while getting the data from the table '${this.store.tableName}': ` + error._message))
                    // database does not exist, therefore create one
                    } else {
                        const table = this.getSoundsTableSchema()
                        this.store.dbConnection.createDb({ Name: this.store.dbName, Tables: [table] })

                        // load sounds from the server and store them into the store
                        this.fetchSounds(instrument)
                    }
                },
                (error) => console.log(error._message))
        },
        getSoundsTableSchema () {
            return {
                Name: this.store.tableName,
                Columns: [
                    {
                        Name: 'id',
                        PrimaryKey: true,
                        AutoIncrement: true
                    },
                    {
                        Name: 'pitch',
                        NotNull: true,
                        DataType: 'string'
                    },
                    {
                        Name: 'instrument',
                        NotNull: true,
                        DataType: 'string'
                    },
                    {
                        Name: 'data',
                        NotNull: true,
                        DataType: 'string'
                    }
                ]
            }
        },
        fetchSounds (instrument) {
            axios.get(`/soundfonts/${instrument}.json`)
                .then(response => {
                    this.sounds = response.data
                    this.initializeAudio()

                    const data = Object.keys(response.data).map(pitch => {
                        return { pitch: pitch, instrument: instrument, data: response.data[pitch] }
                    })

                    // store data into the store
                    this.store.dbConnection.insert({ Into: this.store.tableName, Values: data },
                        (rowsAdded) => {
                            if (rowsAdded > 0) {
                                console.log(`${rowsAdded} records added to the '${this.store.tableName}' table.`)
                            }
                        },
                        (error) => console.log(`An error occurred while adding the data to the table '${this.store.tableName}': ` + error._message))
                })
                .catch(error => console.log(error))
        },
        initializeAudio () {
            this.audios = []
            for (let i = 0; i < this.maxAudios; i++) {
                let audio = new Audio() // eslint-disable-line no-undef
                this.audios.push(audio)
            }
        },
        play (pitch) {
            for (let audio of this.audios) {
                if (!audio.paused) {
                    continue
                }
                audio.currentTime = 0
                audio.src = this.sounds[pitch]
                audio.play()
                break
            }
        },
        startPlaying (notes) {
            if (!this.playing) {
                this.playing = true

                for (let i = 0; i < notes.length; i++) {
                    this.timeoutIds.push(setTimeout(() => this.play(notes[i]), i * this.delay))
                }

                this.playingTimeoutId = setTimeout(() => { this.playing = false }, notes.length * this.delay)
            }
        },
        stopPlaying () {
            for (let timeoutId of this.timeoutIds) {
                clearTimeout(timeoutId)
            }
            this.timeoutIds = []

            clearTimeout(this.playingTimeoutId)
            this.playing = false

            for (let audio of this.audios) {
                if (!audio.paused) {
                    audio.pause()
                    audio.currentTime = 0
                }
            }
        }
    }
}
</script>
