<style lang="scss" scoped>
@import '../../sass/app';

.header {
    width            : 100%;
    height           : 50px;
    padding          : 0 10px;
    display          : flex;
    justify-content  : space-between;
    align-items      : center;
    background-color : $conifer;
}

.header__title {
    color       : $black;
    font-size   : 30px;
    font-family : $font-title;
    cursor      : pointer;
}

.header__user { display: flex; }

.header__username {
    font-size   : 15px;
    font-family : $font-light;
}

.header__logout { margin-left: 15px; }

.header__logout-icon {
    width      : 20px;
    height     : 20px;
    color      : $black;
    opacity    : 0.6;
    cursor     : pointer;
    transition : opacity 0.1s linear;

    @include breakpoint-tablet { opacity : 1; }
    @include breakpoint-phone  { opacity : 1; }

    &:hover { opacity: 1; }
}

.header__logout-form { display: none; }
</style>

<template>
    <div class="header">
        <div class="header__title" @click="dashboard">TRUBADUR</div>
        <div class="header__user">
            <div class="header__username">{{ username }}</div>
            <div class="header__logout">
                <icon class="header__logout-icon" name="sign-out" @click.native="logout($event)"></icon>
                <form id="header__logout-form" action="logout" method="POST">
                    <input type="hidden" name="_token" :value="csrfToken">
                </form>
            </div>
        </div>
    </div>
</template>

<script>
import 'vue-awesome/icons/sign-out'
import { mapState, mapActions } from 'vuex'

export default {
    data () {
        return {
            csrfToken: window.Laravel.csrfToken
        }
    },
    created () {
        this.fetchUser()
    },
    computed: {
        ...mapState(['user']),
        username () {
            return this.user ? this.user.name : ''
        }
    },
    methods: {
        ...mapActions(['fetchUser']),
        dashboard () {
            this.$router.push({ name: 'dashboard' })
        },
        logout (event) {
            event.preventDefault()
            this.$el.querySelector('#header__logout-form').submit()
        }
    }
}
</script>
