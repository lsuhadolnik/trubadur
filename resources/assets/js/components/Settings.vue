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
        left             : 92%;
        border           : 5px solid transparent;
        border-top-color : $black;
        pointer-events   : none;
    }
}

.settings__select-wrapper--disabled {
    border-color : rgba(119, 119, 128, 0.2);
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

.settings__slider {
    width              : 100%;
    height             : 15px;
    border-radius      : 5px;
    background-color   : #d3d3d3;
    outline            : none;
    opacity            : 0.7;
    -webkit-appearance : none;
    -webkit-transition : .2s;
    transition         : opacity 0.2s linear;

    &:hover { opacity: 1; }

    &::-webkit-slider-thumb {
        width              : 25px;
        height             : 25px;
        border-radius      : 50%;
        background-color   : #4CAF50;
        -webkit-appearance : none;
        appearance         : none;
        cursor             : pointer;
    }

    &::-moz-range-thumb {
        width            : 25px;
        height           : 25px;
        border-radius    : 50%;
        background-color : #4CAF50;
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
            <div @click="save()" style="border: 1px solid black; padding: 5px;">SAVE</div>
            <!-- <input type="range" min="1" max="100" value="50" class="slider" id="myRange"> -->
            <!-- <div class="profile__user-info">Ime: {{ name }}</div>
            <div class="profile__user-info">E-mail: {{ email }}</div>
            <img class="profile__avatar" :src="avatar"/>
            <div class="profile__user-info">Št. točk: {{ rating }}</div>
            <div class="profile__user-info">Inštrument: {{ instrument }}</div>
            <div class="profile__user-info">Šola: {{ school }}</div>
            <div class="profile__user-info">Razred: {{ grade }}</div> -->
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    data () {
        return {
            title: 'Nastavitve',
            selectedSchool: null,
            selectedGrade: null,
            filteredGrades: []
        }
    },
    created () {
        this.fetchMe().then(() => {
            this.fetchSchools().then(() => {
                this.fetchGrades().then(() => {
                    this.selectedSchool = this.schools.filter((school) => school.id === this.me.school.id)[0]
                    this.filteredGrades = this.grades.filter(grade => this.selectedSchool.grades.indexOf(grade.id) >= 0)
                    this.selectedGrade = this.grades.filter((grade) => grade.id === this.me.grade.id)[0]
                })
            })
        })
    },
    computed: {
        ...mapState(['me', 'schools', 'grades']),
        loading () {
            return !this.me || !this.schools || !this.grades
        },
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

            this.storeMe(data)
        }
    }
}
</script>
