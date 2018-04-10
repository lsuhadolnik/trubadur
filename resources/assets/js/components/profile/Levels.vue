<style lang="scss" scoped>
@import '../../../sass/variables/index';

.levels { width: 100%; }

.levels__content {
    width          : 100%;
    padding        : 20px 0 $bottom-padding 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.levels__user-info {
    width            : 100%;
    padding          : 20px 10%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $tacao;
}

.levels__avatar {
    width  : 120px;
    height : 120px;
}

.levels__name {
    width           : 100%;
    padding         : 5px 0;
    text-align      : center;
    font-size       : 25px;
}

.levels__level-rating-wrapper {
    width           : 100%;
    display         : flex;
    justify-content : space-between;
}

.levels__level-rating {
    width           : 100px;
    height          : 35px;
    border-radius   : 10px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    text-align      : center;
}

.levels__level  { background-color: $neptune; }
.levels__rating { background-color: $cabaret; }

.levels__level-progress-wrapper {
    width            : 100%;
    padding          : 25px 10%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $jaffa;
}

.levels__level-progress-label {
    color     : $espresso;
    font-size : 15px;
}

.levels__level-progress-bar {
    position          : relative;
    width             : 100%;
    height            : 25px;
    margin-top        : 20px;
    border-radius     : 10px;
    background-color : $tacao;
}

.levels__level-progress-bar-overlay {
    position          : absolute;
    width             : 50%;
    height            : 25px;
    border-radius     : 10px;
    background-image  : url('/images/backgrounds/progress.png');
    background-repeat : repeat;
    background-size   : contain;
    background-color  : $golden-tainoi;
}

.levels__level-progress-bar-labels {
    width           : 100%;
    margin-top      : 10px;
    display         : flex;
    justify-content : space-between;
}

.levels__latest-badges-wrapper {
    width            : 100%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
}

.levels__latest-badges-label-wrapper {
    width            : 100%;
    padding          : 5px 10%;
    display          : flex;
    justify-content  : center;
    background-color : $jaffa;
}

.levels__latest-badges-label {
    color     : $espresso;
    font-size : 15px;
}

.levels__latest-badges {
    width            : 100%;
    min-height       : 100px;
    padding          : 10px 10%;
    display          : flex;
    justify-content  : center;
    align-items      : center;
    background-color : $moss-green;
    cursor           : pointer;
}

.levels__latest-badge-image {
    width      : calc(100% / 3);
    height     : 100%;
    max-height : 150px;
}

.levels__elements {
    width            : 100%;
    padding          : 40px 10%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $jaffa;
}

.levels__element-label-wrapper {
    width         : 100%;
    margin-bottom : 5px;
    padding-left  : 2px;
}

.levels__element-label {
    color     : $espresso;
    font-size : 15px;
}

.levels__element {
    width         : 100%;
    margin-bottom : 20px;
}

.levels__text-label { font-size: 18px; }

.levels__input {
    width                 : 100%;
    height                : 41px;
    padding               : 0 10px;
    border                : none;
    -webkit-border-radius : 0;
    border-radius         : 0;
    caret-color           : $black;
    background-color      : $tacao;
    color                 : $black;
    font-size             : 18px;
    font-family           : $font-bold, sans-serif;
    outline               : none;
    -webkit-user-select   : auto;
    -moz-user-select      : auto;
    -ms-user-select       : auto;
    user-select           : auto;

    &:-webkit-autofill,
    &:-webkit-autofill:hover,
    &:-webkit-autofill:focus { -webkit-box-shadow: 0 0 0 100px $tacao inset !important; }
}

.levels__select-wrapper {
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

.levels__select {
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

.levels__select-arrow {
    position       : absolute;
    top            : 17px;
    right          : -8px;
    width          : 40px;
    height         : 40px;
    fill           : $espresso;
    z-index        : 1;
    pointer-events : none;
}

.levels__commands {
    width          : 100%;
    margin-top     : 20px;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.levels__command--save { margin-top: 15px; }
</style>

<!-- override -->
<style lang="scss">
@import '../../../sass/variables/index';

.levels__commands {
    .levels__command--edit   .button .button__full { background-color : $tacao;   }
    .levels__command--cancel .button .button__full { background-color : $cabaret; }
    .levels__command--save   .button .button__full { background-color : $tacao;   }
}
</style>

<template>
    <div class="levels">
        <loader v-show="loading"></loader>
        <div class="levels__content" v-show="!loading">

            <div class="levels__user-info">
                <img class="levels__avatar" id="avatar"/>
                <label class="levels__name">{{ name }}</label>
                <div class="levels__level-rating-wrapper">
                    <div class="levels__level-rating levels__level">NIVO {{ levelLevel }}</div>
                    <div class="levels__level-rating levels__rating">{{ rating }}</div>
                </div>
            </div>

            <div class="levels__level-progress-wrapper">
                <label class="levels__level-progress-label">KJE SEM</label>
                <div class="levels__level-progress-bar">
                    <div class="levels__level-progress-bar-overlay" :style="{ 'width': ((levelMaxRating - rating) * 100 / (levelMaxRating - levelMinRating)) + '%' }"></div>
                </div>
                <div class="levels__level-progress-bar-labels">
                    <label class="levels__level-progress-label">NIVO {{ levelLevel }}</label>
                    <label class="levels__level-progress-label">NIVO {{ levelLevel + 1 }}</label>
                </div>
            </div>

            <div class="levels__latest-badges-wrapper">
                <div class="levels__latest-badges-label-wrapper">
                    <label class="levels__latest-badges-label">ZADNJI DOSEŽKI</label>
                </div>
                <div class="levels__latest-badges" @click="openTab('badges')">
                    <img class="levels__latest-badge-image" :id="'latest_badge_' + n" v-for="n in nLatestBadges"/>
                    <label class="levels__latest-badges-label" v-if="!hasLatestBadges">Ni dosežkov.</label>
                </div>
            </div>

            <div class="levels__elements">
                <div class="levels__element-label-wrapper">
                    <label class="levels__element-label">UPORABNIŠKO IME</label>
                </div>
                <div class="levels__element" v-show="!editing">
                    <label class="levels__text-label">{{ name }}</label>
                </div>
                <input class="levels__element levels__input" type="text" v-model="selectedName" v-show="editing"/>

                <div class="levels__element-label-wrapper">
                    <label class="levels__element-label">ŠOLA</label>
                </div>
                <div class="levels__element" v-show="!editing">
                    <label class="levels__text-label">{{ school }}</label>
                </div>
                <div class="levels__element levels__select-wrapper" v-show="editing">
                    <select class="levels__select" v-model="selectedSchool" @change="onSchoolSelected()">
                        <option class="levels__option" :value="school" v-for="school in schools">{{ school.name }}</option>
                    </select>
                    <svg class="levels__select-arrow" viewBox="0 0 100 100">
                       <use id="arrow_school"></use>
                    </svg>
                </div>

                <div class="levels__element-label-wrapper">
                    <label class="levels__element-label">LETNIK</label>
                </div>
                <div class="levels__element" v-show="!editing">
                    <label class="levels__text-label">{{ grade }}.</label>
                </div>
                <div class="levels__element levels__select-wrapper" v-show="editing">
                    <select class="levels__select" v-model="selectedGrade">
                        <option class="levels__option" :value="grade" v-for="grade in filteredGrades">{{ grade.grade }}</option>
                    </select>
                    <svg class="levels__select-arrow" viewBox="0 0 100 100">
                       <use id="arrow_grade"></use>
                    </svg>
                </div>

                <div class="levels__commands" v-if="owner">
                    <div class="levels__command--edit">
                        <element-button text="uredi" @click.native="edit()" v-show="!editing"></element-button>
                    </div>
                    <div class="levels__command--cancel">
                        <element-button text="prekliči" @click.native="cancel()" v-show="editing"></element-button>
                    </div>
                    <div class="levels__command--save">
                        <element-button text="shrani" @click.native="update()" v-show="editing"></element-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    props: ['id'],
    data () {
        return {
            title: 'Profil',
            loading: true,
            editing: false,
            owner: false,
            user: null,
            level: null,
            nLatestBadges: 3,
            userBadges: [],
            selectedName: '',
            selectedSchool: null,
            selectedGrade: null,
            filteredGrades: []
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchMe().then(() => {
                this.fetchUser(this.id).then((user) => {
                    this.user = user
                    this.owner = this.me.id === this.user.id

                    this.fetchLevel({ rating: this.user.rating }).then((level) => {
                        this.level = level

                        this.fetchUserBadges({ per_page: this.nLatestBadges, page: 1, order_by: 'updated_at', order_direction: 'desc', filter_user_id: this.user.id, filter_completed: 1 }).then((data) => {
                            this.userBadges = data.data

                            if (this.owner) {
                                this.fetchSchools().then(() => {
                                    this.fetchGrades().then(() => {
                                        this.loadImages()
                                    })
                                })
                            } else {
                                this.loadImages()
                            }
                        })
                    })
                })
            })
        })
    },
    computed: {
        ...mapState(['me', 'schools', 'grades']),
        name () {
            return this.user ? this.user.name : ''
        },
        email () {
            return this.user ? this.user.email : ''
        },
        rating () {
            return this.user ? this.user.rating : 0
        },
        instrument () {
            return this.user ? this.user.instrument : ''
        },
        school () {
            return this.user ? this.user.school.name : ''
        },
        grade () {
            return this.user ? this.user.grade.grade : ''
        },
        levelLevel () {
            return this.level ? this.level.level : 0
        },
        levelMinRating () {
            return this.level ? this.level.min_rating : 0
        },
        levelMaxRating () {
            return this.level ? this.level.max_rating : 0
        },
        hasLatestBadges () {
            return this.userBadges.length > 0
        }
    },
    methods: {
        ...mapActions(['fetchMe', 'updateMe', 'fetchUser', 'fetchLevel', 'fetchUserBadges', 'fetchSchools', 'fetchGrades']),
        loadImages () {
            const context = this

            let nLoaded = 0
            const nTotal = 2 + this.userBadges.length

            const avatar = this.$el.querySelector('#avatar')
            avatar.onload = () => {
                if (++nLoaded === nTotal) {
                    context.loading = false
                }
            }
            avatar.src = this.user.avatar

            for (let i = 0; i < this.nLatestBadges; i++) {
                let badge = this.$el.querySelector('#latest_badge_' + (i + 1))
                if (i < this.userBadges.length) {
                    badge.onload = () => {
                        if (++nLoaded === nTotal) {
                            context.loading = false
                        }
                    }
                    badge.src = this.userBadges[i].badge.image
                } else {
                    badge.style.display = 'none'
                }
            }

            const arrowSchool = this.$el.querySelector('#arrow_school')
            const arrowGrade = this.$el.querySelector('#arrow_grade')
            arrowSchool.onload = () => {
                if (++nLoaded === nTotal) {
                    context.loading = false
                }
            }
            arrowSchool.href.baseVal = '/images/arrows/down.svg#element'
            arrowGrade.href.baseVal = '/images/arrows/down.svg#element'
        },
        filterGrades () {
            this.filteredGrades = this.grades.filter(grade => this.selectedSchool.grades.indexOf(grade.id) >= 0)
        },
        onSchoolSelected () {
            this.filterGrades()
            this.selectedGrade = this.filteredGrades[0]
        },
        edit () {
            this.selectedName = this.user.name
            this.selectedSchool = this.schools.filter((school) => school.id === this.user.school.id)[0]
            this.selectedGrade = this.grades.filter((grade) => grade.id === this.user.grade.id)[0]
            this.filterGrades()

            this.editing = true
        },
        cancel () {
            this.editing = false
        },
        update () {
            const data = {}

            if (this.selectedName !== this.user.name) {
                data['name'] = this.selectedName
            }

            if (this.selectedSchool.id !== this.user.school.id) {
                data['school_id'] = this.selectedSchool.id
            }

            if (this.selectedGrade.id !== this.user.grade.id) {
                data['grade_id'] = this.selectedGrade.id
            }

            this.editing = false

            if (Object.keys(data).length > 0) {
                this.loading = true
                this.updateMe(data).then(() => {
                    this.fetchUser(this.user.id).then((user) => {
                        this.user = user
                        this.loading = false
                    })
                })
            }
        }
    }
}
</script>
