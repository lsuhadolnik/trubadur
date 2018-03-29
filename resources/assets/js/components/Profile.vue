<style lang="scss" scoped>
@import '../../sass/app';

.profile {
    width            : 100%;
    height           : calc(100% - 50px);
    padding          : 10px;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $aero-blue;
}

.profile__loader {
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

.profile__title {
    font-family : $font-regular;
    font-size   : 30px;
    font-weight : bold;
}

.profile__user-info {
    font-family : $font-regular;
    font-size   : 20px;
}

.profile__avatar {
    width  : 100px;
    height : 100px;
}
</style>

<template>
    <div class="profile">
        <div class="profile__title">{{ title | uppercase }}</div>
        <img class="profile__loader" src="/images/loader.svg" v-show="loading"/>
        <div v-show="!loading">
            <div class="profile__user-info">Ime: {{ name }}</div>
            <div class="profile__user-info">E-mail: {{ email }}</div>
            <img class="profile__avatar" :src="avatar"/>
            <div class="profile__user-info">Šola: {{ school }}</div>
            <div class="profile__user-info">Razred: {{ grade }}</div>
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
    computed: {
        name () {
            return this.user ? this.user.name : ''
        },
        email () {
            return this.user ? this.user.email : ''
        },
        avatar () {
            return this.user ? this.user.avatar : ''
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
    created () {
        this.fetchUser(this.id).then((user) => {
            this.user = user
            this.loading = false
        })
    },
    methods: {
        ...mapActions(['fetchUser'])
    }
}
</script>
