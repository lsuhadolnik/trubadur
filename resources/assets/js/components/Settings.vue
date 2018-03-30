<style lang="scss" scoped>
@import '../../sass/app';

.settings {
    width            : 100%;
    height           : calc(100% - 50px);
    padding          : 10px;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $aero-blue;
}

.settings__loader {
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

.settings__title {
    font-family : $font-regular;
    font-size   : 30px;
    font-weight : bold;
}

.settings__select-wrapper {
    width            : 100%;
    height           : 100%;
    position         : relative;
    border           : 1px solid $dolphin-transparent;
    background-color : transparent;
    cursor           : pointer;
    transition       : border-color 0.1s linear;

    &:hover:not(.settings__select-wrapper--disabled) { border-color: $blue; }

    &:after {
        position         : absolute;
        content          : '';
        width            : 0;
        height           : 0;
        top              : 43%;
        left             : 94%;
        border           : 5px solid transparent;
        border-top-color : $black;
        pointer-events   : none;
    }
}

.settings__select-wrapper--disabled {
    border-color : $dolphin-very-transparent;
    cursor       : not-allowed;
}

.settings__select {
    width              : 100%;
    height             : 100%;
    padding            : 5px 30px 5px 5px;
    border             : none;
    border-radius      : 0;
    background-color   : transparent;
    color              : $very-dark-gray;
    font-size          : 14px;
    font-family        : $font-light;
    white-space        : nowrap;
    text-overflow      : ellipsis;
    overflow           : hidden;
    -moz-appearance    : none;
    -webkit-appearance : none;
    outline            : none;
    cursor             : pointer;
    transition         : color 0.1s linear;

    &:hover:not(:disabled), &:focus:not(:disabled) { color: $black; }

    &:disabled {
        background-color : $very-light-gray;
        opacity          : 0.35;
        cursor           : not-allowed;
    }
}

.settings__slider-wrapper { display: flex; }

.settings__slider-label { width: 20%; }

.settings__slider {
    width              : 80%;
    height             : 15px;
    border-radius      : 5px;
    background-color   : $silver-chalice;
    outline            : none;
    opacity            : 0.7;
    -webkit-appearance : none;
    appearance         : none;
    transition         : opacity 0.2s linear;

    &:hover { opacity: 1; }

    &::-webkit-slider-thumb {
        width              : 25px;
        height             : 25px;
        border-radius      : 50%;
        background-color   : $egg-blue;
        -webkit-appearance : none;
        appearance         : none;
        cursor             : pointer;
    }

    &::-moz-range-thumb {
        width            : 25px;
        height           : 25px;
        border-radius    : 50%;
        background-color : $egg-blue;
        cursor           : pointer;
    }
}
</style>

<template>
    <div class="settings">
        <div class="settings__title">{{ title | uppercase }}</div>
        <img class="settings__loader" src="/images/loader.svg" v-show="loading"/>
        <div v-show="!loading">
            <div class="settings__select-wrapper" :class="{ 'settings__select-wrapper--disabled': !hasSchools }">
                <select class="settings__select" v-model="selectedSchool" @change="onSchoolSelected()">
                    <option class="settings__option" :value="school" v-for="school in schools">{{ school.name }}</option>
                </select>
            </div>
            <div class="settings__select-wrapper" :class="{ 'settings__select-wrapper--disabled': !hasGrades }">
                <select class="settings__select" v-model="selectedGrade">
                    <option class="settings__option" :value="grade" v-for="grade in filteredGrades">{{ grade.grade }}</option>
                </select>
            </div>
            <div class="settings__select-wrapper">
                <select class="settings__select" v-model="selectedInstrument">
                    <option class="settings__option" :value="key" v-for="(value, key) in instruments">{{ value }}</option>
                </select>
            </div>
            <div class="settings__select-wrapper">
                <select class="settings__select" v-model="selectedClef">
                    <option class="settings__option" :value="key" v-for="(value, key) in clefs">{{ value }}</option>
                </select>
            </div>
            <div class="settings__slider-wrapper">
                <label for="notePlaybackDelay" class="settings__slider-label">{{ selectedNotePlaybackDelay }} s</label>
                <input type="range" class="settings__slider" id="notePlaybackDelay" min="0.5" max="2.5" step="0.1" v-model="selectedNotePlaybackDelay"/>
            </div>


            <div @click="save()" style="border: 1px solid black; padding: 5px; cursor: pointer;">SAVE</div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    data () {
        return {
            title: 'Nastavitve',
            loading: true,
            selectedSchool: null,
            selectedGrade: null,
            filteredGrades: [],
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
            selectedNotePlaybackDelay: 0.5
        }
    },
    created () {
        this.fetchMe().then(() => {
            this.selectedInstrument = this.me.instrument
            this.selectedClef = this.me.clef
            this.selectedNotePlaybackDelay = this.me.note_playback_delay / 1000

            this.fetchSchools().then(() => {
                this.fetchGrades().then(() => {
                    this.selectedSchool = this.schools.filter((school) => school.id === this.me.school.id)[0]
                    this.filteredGrades = this.grades.filter(grade => this.selectedSchool.grades.indexOf(grade.id) >= 0)
                    this.selectedGrade = this.grades.filter((grade) => grade.id === this.me.grade.id)[0]

                    this.loading = false
                })
            })
        })
    },
    computed: {
        ...mapState(['me', 'schools', 'grades']),
        hasSchools () {
            return !this.loading && this.schools.length > 0
        },
        hasGrades () {
            return !this.loading && this.filteredGrades.length > 0
        }
    },
    methods: {
        ...mapActions(['fetchMe', 'storeMe', 'fetchSchools', 'fetchGrades']),
        onSchoolSelected () {
            this.filteredGrades = this.grades.filter(grade => this.selectedSchool.grades.indexOf(grade.id) >= 0)
            this.selectedGrade = this.filteredGrades[0]
        },
        save () {
            const data = {}

            if (this.selectedSchool.id !== this.me.school.id) {
                data['school_id'] = this.selectedSchool.id
            }

            if (this.selectedGrade.id !== this.me.grade.id) {
                data['grade_id'] = this.selectedGrade.id
            }

            if (this.selectedInstrument !== this.me.instrument) {
                data['instrument'] = this.selectedInstrument
            }

            if (this.selectedClef !== this.me.clef) {
                data['clef'] = this.selectedClef
            }

            if (this.selectedNotePlaybackDelay !== this.me.note_playback_delay) {
                data['note_playback_delay'] = parseFloat(this.selectedNotePlaybackDelay) * 1000
            }

            this.storeMe(data)
        }
    }
}
</script>
