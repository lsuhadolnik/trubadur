<style lang="scss" scoped>
@import '../../sass/variables/index';

.header {
    width            : 100%;
    height           : $header-height;
    display          : flex;
    justify-content  : center;
    align-items      : center;
    background-color : transparent;
    transition       : background-color $menu-animation-time linear;
}

.header--sticky {
    position         : fixed;
    top              : 0;
    left             : 0;
    background-color : $sunglow;
    z-index          : 100;
}

.header--colored { background-color: $sunglow; }

.header__menu-button {
    position : absolute;
    top      : 15px;
    left     : 20px;
    padding  : 5px;
    cursor   : pointer;
}

.header__icon {
    width  : 30px;
    height : 30px;
}

.header__title {
    color       : $black;
    font-size   : 25px;
    font-family : $font-bold;
    cursor      : pointer;
}

.menu {
    position         : fixed;
    top              : 70px;
    left             : -$menu-width;
    width            : $menu-width;
    height           : 100vh;
    padding          : 10px 0;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $sunglow;
    z-index          : 100;
}

.menu--open {
    animation : open $menu-animation-time ease-out 0s forwards;

    @keyframes open {
        from { left : -$menu-width; }
        to   { left : 0;            }
    }
}

.menu--close {
    animation : close $menu-animation-time ease-in 0s;

    @keyframes close {
        from { left : 0;            }
        to   { left : -$menu-width; }
    }
}

.menu__item {
    width            : calc(#{$menu-width} * 0.8);
    height           : calc(#{$menu-width} * 0.8);
    display          : flex;
    align-items      : center;
    justify-content  : center;
    flex-direction   : column;
    border-radius    : 10px;
    cursor           : pointer;
    background-color : transparent;

    &:hover { background-color: $golden-tainoi; }
}

.menu-item--active { background-color: $golden-tainoi; }

.menu__image {
    width  : 65px;
    height : 65px;
}

.menu__label { font-size: 14px; }

.menu__logout-form { display: none; }
</style>

<template>
    <div class="header-menu">
        <div class="header" :class="{ 'header--sticky': isHeaderSticky, 'header--colored': isHeaderColored }">
            <div class="header__menu-button" @click="toggleMenu($event)">
                <icon class="icon header__icon" name="bars"></icon>
            </div>
            <div class="header__title" @click="dashboard()">TRUBADUR</div>
        </div>
        <div class="menu" :class="{ 'menu--open': isMenuInitialized && isMenuOpened, 'menu--close': isMenuInitialized && !isMenuOpened }" v-click-outside="clickedOutsideMenu">
            <div class="menu__item" :class="{ 'menu-item--active': isItemActive(item) }" v-for="item in menuItems" @click="open($event, item)">
                <img class="menu__image" :src="'/images/menu/' + item.image + '.svg'"></img>
                <label class="menu__label">{{ item.name | uppercase }}</label>
            </div>
            <form id="menu__logout-form" action="/logout" method="POST">
                <input type="hidden" name="_token" :value="csrfToken"/>
            </form>
        </div>
    </div>
</template>

<script>
import 'vue-awesome/icons/bars'
import 'vue-awesome/icons/sign-out'
import { mapState } from 'vuex'

export default {
    data () {
        return {
            csrfToken: window.Laravel.csrfToken,
            isHeaderSticky: false,
            isHeaderColored: false,
            isMenuInitialized: false,
            isMenuOpened: false,
            menuItems: [
                { name: 'igra', route: 'gameTypes', image: 'game' },
                { name: 'profil', route: 'profile', image: 'profile' },
                { name: 'lestvica', route: 'leaderboard', image: 'leaderboard' },
                { name: 'nastavitve', route: 'settings', image: 'settings' },
                { name: 'odjava', route: 'logout', image: 'logout' }
            ]
        }
    },
    created () {
        window.addEventListener('scroll', this.scroll)
        this.colorHeader(this.$route.name)
    },
    beforeDestroy () {
        window.removeEventListener('scroll', this.scroll)
    },
    computed: {
        ...mapState(['me']),
        userId () {
            return this.user ? this.me.id : 0
        }
    },
    watch: {
        '$route' (to, from) {
            this.colorHeader(to.name)
        }
    },
    methods: {
        colorHeader (route) {
            this.isHeaderColored = ['gameModes', 'intervals', 'gameStatistics'].indexOf(route) >= 0
        },
        scroll () {
            this.isHeaderSticky = this.isMenuOpened ? true : window.pageYOffset > 0
            this.$emit('sticky-header', this.isHeaderSticky)
        },
        toggleMenu (event) {
            this.isMenuInitialized = true
            this.isMenuOpened = !this.isMenuOpened
            this.scroll()
            event.stopPropagation()
        },
        clickedOutsideMenu (event) {
            if (this.isMenuOpened) {
                this.isMenuOpened = false
                this.scroll()
            }
        },
        isItemActive (item) {
            return item.route === this.$route.name
        },
        open (event, item) {
            let func

            switch (item.route) {
                case 'gameTypes':
                case 'leaderboard':
                case 'settings':
                    func = this.reroute(item.route)
                    break
                case 'profile':
                    func = this.reroute(item.route, { id: this.userId })
                    break
                case 'logout':
                    func = this.logout()
                    break
            }

            this.toggleMenu(event)
            setTimeout(() => func, 0.2 * 1000)
        },
        dashboard () {
            this.isMenuOpened = false
            this.scroll()
            this.reroute('dashboard')
        },
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        },
        logout (event) {
            this.$el.querySelector('#menu__logout-form').submit()
        }
    }
}
</script>
