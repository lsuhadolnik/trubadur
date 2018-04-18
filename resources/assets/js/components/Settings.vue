<style lang="scss" scoped>
@import '../../sass/variables/index';

$instructions-height        : 290px;

.settings { width: 100%; }

.settings__content {
    width          : 100%;
    height         : 100%;
    padding-bottom : $bottom-padding;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.settings__instructions {
    width          : 100%;
    height         : $instructions-height;
    padding-top    : 10px;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.settings__image {
    width         : 120px;
    height        : 120px;
    margin-bottom : 10px;
}

.settings__label { margin: $label-margin; }

.settings__arrow {
    width  : 30px;
    height : 30px;
}

.settings__elements {
    width            : 100%;
    margin-top       : $content-margin;
    padding          : 40px 10%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $jaffa;
}

.settings__element-label-wrapper {
    width         : 100%;
    margin-bottom : 5px;
    padding-left  : 2px;
}

.settings__element-label {
    color     : $espresso;
    font-size : 15px;
}

.settings__element { margin-bottom: 20px; }

.settings__select-wrapper {
    position : relative;
    width    : 100%;
    height   : 100%;
    cursor   : pointer;

    &:after {
        content          : '';
        position         : absolute;
        top              : 0;
        right            : 0;
        width            : $select-arrow-wrapper-width;
        height           : 100%;
        background-color : $sandy-brown;
        pointer-events   : none;
    }
}

.settings__select {
    width              : 100%;
    height             : 100%;
    padding            : 10px 30px 10px 10px;
    border             : none;
    border-radius      : 0;
    background-color   : $tacao;
    font-size          : 18px;
    font-family        : $font-bold, sans-serif;
    overflow           : hidden;
    outline            : none;
    -moz-appearance    : none;
    -webkit-appearance : none;
    appearance         : none;
    cursor             : pointer;
}

.settings__select-arrow {
    position       : absolute;
    top            : 17px;
    right          : -8px;
    width          : 40px;
    height         : 40px;
    fill           : $espresso;
    z-index        : 1;
    pointer-events : none;
}

.settings__slider-wrapper {
    width   : 100%;
    display : flex;
}

.settings__slider-label { width: 60px; }

.settings__slider {
    width              : calc(100% - 60px);
    height             : 15px;
    border-radius      : 5px;
    background-color   : $tacao;
    opacity            : 0.7;
    outline            : none;
    -moz-appearance    : none;
    -webkit-appearance : none;
    appearance         : none;
    cursor             : pointer;

    @include breakpoint-tablet { opacity : 1; }
    @include breakpoint-phone  { opacity : 1; }

    &:hover { opacity: 1; }

    &::-webkit-slider-thumb {
        width              : 25px;
        height             : 25px;
        border-radius      : 50%;
        background-color   : $espresso;
        -moz-appearance    : none;
        -webkit-appearance : none;
        appearance         : none;
        cursor             : pointer;
    }

    &::-moz-range-thumb {
        width            : 25px;
        height           : 25px;
        border-radius    : 50%;
        background-color : $espresso;
        cursor           : pointer;
    }
}
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.settings__elements .button {
    margin-top: 50px;

    .button__full { background-color: $tacao; }
}
</style>

<template>
    <div class="settings">
        <loader v-show="loading"></loader>
        <div class="settings__content" v-show="!loading">
            <div class="settings__instructions">
                <img class="settings__image" id="image"/>
                <element-title text="nastavitve"></element-title>
                <label class="settings__label">Uredi svoje nastavitve za igro.</label>
                <img class="settings__arrow" id="arrow"/>
            </div>
            <div class="settings__elements">
                <div class="settings__element-label-wrapper">
                    <label class="settings__element-label">INŠTRUMENT</label>
                </div>
                <div class="settings__element settings__select-wrapper">
                    <select class="settings__select" v-model="selectedInstrument">
                        <option class="settings__option" :value="key" v-for="(value, key) in instruments">{{ value }}</option>
                    </select>
                    <svg class="settings__select-arrow" viewBox="0 0 100 100">
                       <use id="arrow_instrument"></use>
                    </svg>
                </div>
                <div class="settings__element-label-wrapper">
                    <label class="settings__element-label">KLJUČ</label>
                </div>
                <div class="settings__element settings__select-wrapper">
                    <select class="settings__select" v-model="selectedClef">
                        <option class="settings__option" :value="key" v-for="(value, key) in clefs">{{ value }}</option>
                    </select>
                    <svg class="settings__select-arrow" viewBox="0 0 100 100">
                       <use id="arrow_clef"></use>
                    </svg>
                </div>
                <div class="settings__element-label-wrapper">
                    <label class="settings__element-label">PAVZA MED PREDVAJANIMI TONI</label>
                </div>
                <div class="settings__element settings__slider-wrapper">
                    <label for="notePlaybackDelay" class="settings__slider-label">{{ selectedNotePlaybackDelay }} s</label>
                    <input type="range" class="settings__slider" id="notePlaybackDelay" :min="minPlaybackDelay" :max="maxPlaybackDelay" step="0.1" v-model="selectedNotePlaybackDelay"/>
                </div>
                <element-button text="shrani" @click.native="save()"></element-button>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    data () {
        return {
            loading: true,
            instruments: {
                guitar: 'Kitara',
                clarinet: 'Klarinet',
                piano: 'Klavir',
                trumpet: 'Trobenta',
                violin: 'Violina'
            },
            selectedInstrument: null,
            clefs: {
                violin: 'Violinski',
                bass: 'Basovski'
            },
            selectedClef: null,
            selectedNotePlaybackDelay: 1,
            minPlaybackDelay: 1.5,
            maxPlaybackDelay: 5
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchMe().then(() => {
                this.selectedInstrument = this.me.instrument
                this.selectedClef = this.me.clef
                this.selectedNotePlaybackDelay = this.me.note_playback_delay / 1000

                this.loadImages()
            })
        })
    },
    computed: {
        ...mapState(['me'])
    },
    methods: {
        ...mapActions(['fetchMe', 'updateMe']),
        loadImages () {
            const context = this

            let nLoaded = 0
            const nTotal = 2

            const image = this.$el.querySelector('#image')
            image.onload = () => {
                if (++nLoaded === nTotal) {
                    this.loadAdditionalImages()
                    context.loading = false
                }
            }
            image.src = '/images/settings/settings.svg'

            const arrow = this.$el.querySelector('#arrow')
            arrow.onload = () => {
                if (++nLoaded === nTotal) {
                    this.loadAdditionalImages()
                    context.loading = false
                }
            }
            arrow.src = '/images/arrows/down.svg'
        },
        loadAdditionalImages () {
            const arrowInstrument = this.$el.querySelector('#arrow_instrument')
            arrowInstrument.href.baseVal = '/images/arrows/down.svg#element'

            const arrowClef = this.$el.querySelector('#arrow_clef')
            arrowClef.href.baseVal = '/images/arrows/down.svg#element'
        },
        save () {
            const data = {}

            if (this.selectedInstrument !== this.me.instrument) {
                data['instrument'] = this.selectedInstrument
            }

            if (this.selectedClef !== this.me.clef) {
                data['clef'] = this.selectedClef
            }

            if (this.selectedNotePlaybackDelay !== this.me.note_playback_delay) {
                data['note_playback_delay'] = parseFloat(this.selectedNotePlaybackDelay) * 1000
            }

            if (Object.keys(data).length > 0) {
                this.loading = true
                this.updateMe(data).then(() => {
                    this.loading = false
                })
            }
        }
    }
}
</script>
