<style lang="scss" scoped>
@import '../../sass/variables/index';

.profile { width: 100%; }

.profile__content {
    width          : 100%;
    padding        : 20px 0 $bottom-padding 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.profile__user-info {
    width            : 100%;
    padding          : 20px 10px;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $tacao;
}

.profile__avatar {
    width  : 120px;
    height : 120px;
}

.profile__name {
    padding-bottom  : 10px;
    text-align      : center;
    font-size       : 25px;
}

.profile__level-rating-wrapper {
    width           : 100%;
    display         : flex;
    justify-content : space-between;
}

.profile__level-rating {
    width           : 80px;
    height          : 35px;
    border-radius   : 10px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    text-align      : center;
}

.profile__level  { background-color: $neptune; }
.profile__rating { background-color: $cabaret; }

.profile__elements {
    width            : 100%;
    padding          : 40px 10%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $jaffa;
}

.profile__element-label-wrapper {
    width         : 100%;
    margin-bottom : 5px;
}

.profile__element-label {
    color     : $espresso;
    font-size : 15px;
}

.profile__element {
    width         : 100%;
    margin-bottom : 20px;
}

.profile__text-label { font-size: 18px; }
</style>

<template>
    <div class="profile">
       <loader v-show="loading"></loader>
        <div class="profile__content" v-show="!loading">
            <div class="profile__user-info">
                <label class="profile__name">{{ name }}</label>
                <img class="profile__avatar" id="avatar"/>
                <div class="profile__level-rating-wrapper">
                    <div class="profile__level-rating profile__level">{{ level }}</div>
                    <div class="profile__level-rating profile__rating">{{ rating }}</div>
                </div>
            </div>
            <div class="profile__elements">
                <div class="profile__element-label-wrapper">
                    <label class="profile__element-label">UPORABNIŠKO IME</label>
                </div>
                <div class="profile__element">
                    <label class="profile__text-label">{{ name }}</label>
                </div>
                <div class="profile__element-label-wrapper">
                    <label class="profile__element-label">ŠOLA</label>
                </div>
                <div class="profile__element">
                    <label class="profile__text-label">{{ school }}</label>
                </div>
                <div class="profile__element-label-wrapper">
                    <label class="profile__element-label">LETNIK</label>
                </div>
                <div class="profile__element">
                    <label class="profile__text-label">{{ grade }}.</label>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
    props: ['id'],
    data () {
        return {
            title: 'Profil',
            loading: true,
            user: null
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchUser(this.id).then((user) => {
                this.user = user

                const avatar = this.$el.querySelector('#avatar')
                const context = this
                avatar.onload = () => { context.loading = false }
                avatar.src = user.avatar
            })
        })
    },
    computed: {
        name () {
            return this.user ? this.user.name : ''
        },
        email () {
            return this.user ? this.user.email : ''
        },
        level () {
            return this.user ? 'NIVO 1' : '' // TODO: add levels table to the database (refactor levels -> difficulties)
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
        }
    },
    methods: {
        ...mapActions(['fetchUser'])
    }
}
</script>
