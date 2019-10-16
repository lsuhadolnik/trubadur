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

.header--hidden { display: none; }

.header__menu-button {
    position : absolute;
    top      : 15px;
    left     : 20px;
    padding  : 5px;
    cursor   : pointer;
}

.header__icon {
    width      : 30px;
    height     : 30px;
    transition : opacity $button-transition-time linear;
}

.header__title {
    color       : $black;
    font-size   : 25px;
    cursor      : pointer;
}

.menu {
    position         : fixed;
    top              : 70px;
    left             : -$menu-width;
    width            : $menu-width;
    height           : calc(100vh - #{$header-height});
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $sunglow;
    z-index          : 100;

    overflow-y: auto;
    -webkit-overflow-scrolling: touch;
    

    @include breakpoint-small-phone {
        left  : -$menu-width-small;
        width : $menu-width-small;
    }

    @include breakpoint-large-phone-landscape {
        left                  : -$menu-width * 2;
        width                 : $menu-width * 2;
        padding               : 0 5px 30px 5px;
        display               : grid;
        grid-template-columns : repeat(2, 1fr);
    }

    @include breakpoint-phone-landscape {
        left                  : -$menu-width * 2;
        width                 : $menu-width * 2;
        padding               : 0 5px 30px 5px;
        display               : grid;
        grid-template-columns : repeat(2, 1fr);
    }

    @include breakpoint-small-phone-landscape {
        left                  : -$menu-width-small * 2;
        width                 : $menu-width-small * 2;
        padding               : 0 5px 30px 5px;
        display               : grid;
        grid-template-columns : repeat(2, 1fr);
        grid-row-gap          : 5px;
    }
}

.menu--open {
    animation: open $menu-animation-time ease-out 0s forwards;

    @include breakpoint-small-phone             { animation-name: open-small;           }
    @include breakpoint-landscape               { animation-name: open-landscape;       }
    @include breakpoint-small-phone-landscape   { animation-name: open-small-landscape; }

    @keyframes open {
        from { left : -$menu-width; }
        to   { left : 0;            }
    }

    @keyframes open-small {
        from { left : -$menu-width-small; }
        to   { left : 0;                  }
    }

    @keyframes open-landscape {
        from { left : -$menu-width * 2; }
        to   { left : 0;                }
    }

    @keyframes open-small-landscape {
        from { left : -$menu-width-small * 2; }
        to   { left : 0;                      }
    }
}

.menu--close {
    animation: close $menu-animation-time ease-in 0s;

    @include breakpoint-small-phone             { animation-name: close-small;           }
    @include breakpoint-landscape               { animation-name: close-landscape;       }
    @include breakpoint-small-phone-landscape   { animation-name: close-small-landscape; }

    @keyframes close {
        from { left : 0;            }
        to   { left : -$menu-width; }
    }

    @keyframes close-small {
        from { left : 0;                  }
        to   { left : -$menu-width-small; }
    }

    @keyframes close-landscape {
        from { left : 0;                }
        to   { left : -$menu-width * 2; }
    }

    @keyframes close-small-landscape {
        from { left : 0;                      }
        to   { left : -$menu-width-small * 2; }
    }
}

.menu__item {
    width            : calc(#{$menu-width} * 0.8);
    height           : calc(#{$menu-width} * 0.8);
    grid-column      : span 1;
    justify-self     : center;
    display          : flex;
    align-items      : center;
    justify-content  : center;
    flex-direction   : column;
    border-radius    : 10px;
    cursor           : pointer;
    background-color : transparent;

    @include breakpoint-small-phone {
        width  : calc(#{$menu-width-small} * 0.8);
        height : calc(#{$menu-width-small} * 0.8);
    }

    @include breakpoint-large-phone-landscape {
        width  : calc(#{$menu-width} * 0.85);
        height : calc(#{$menu-width} * 0.75);
    }

    @include breakpoint-phone-landscape {
        width  : calc(#{$menu-width} * 0.9);
        height : calc(#{$menu-width} * 0.75);
    }

    @include breakpoint-small-phone-landscape {
        width  : calc(#{$menu-width-small} * 0.9);
        height : calc(#{$menu-width-small} * 0.7);
    }

    &:hover { background-color: $golden-tainoi; }
}

.menu-item--active { background-color: $golden-tainoi; }

.menu__image {
    width  : 65px;
    height : 65px;

    @include breakpoint-small-phone {
        width  : 50px;
        height : 50px;
    }
}

.menu__label {
    font-size: 12px;

    @include breakpoint-small-phone { font-size: 11px; }
}

.menu__logout-form { display: none; }
</style>

<template>
    <div class="header-menu" v-if="!headerMenuDisabled">
        <div class="header" :class="{ 'header--sticky': isHeaderSticky, 'header--colored': isHeaderColored }">
            <div class="header__menu-button" @click="toggleMenu">
                <icon class="header__icon" name="bars"></icon>
            </div>
            <div class="header__title">TRUBADUR</div>
        </div>
        <div class="menu" :class="{ 'menu--open': isMenuInitialized && isMenuOpened, 'menu--close': isMenuInitialized && !isMenuOpened }" v-click-outside="clickedOutsideMenu" v-touch-outside="clickedOutsideMenu">
            <div class="menu__item" :class="{ 'menu-item--active': isItemActive(item) }" v-for="item in menuItems" :key="item.text" @click="open($event, item)">
                <img v-if="item.image" class="menu__image" :src="'/images/menu/' + item.image + '.svg'"></img>
                <div v-else class="menu__image_text">{{item.text}}</div>
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
import { mapState, mapActions, mapMutations } from 'vuex'

export default {
    data () {
        return {
            csrfToken: window.Laravel.csrfToken,
            backgroundImage: "url('/images/backgrounds/sparse.png')",
            uncoloredRoutes: ['gameModes', 'intervals', 'gameStatistics', 'rhythm'],
            isHeaderSticky: false,
            isHeaderColored: false,
            isHeaderHidden: false,
            isMenuInitialized: false,
            isMenuOpened: false,
            outsideClick: false,
            menuItems: [
                { name: 'igra', route: 'gameTypes', image: 'game' },
                { name: 'profil', route: 'profile', image: 'profile' },
                { name: 'lestvica', route: 'leaderboard', image: 'leaderboard' },
                /*{ name: 'nastavitve', route: 'settings', image: 'settings' },*/
                { name: 'odjava', route: 'logout', image: 'logout' }
            ]
        }
    },
    created () {
        window.addEventListener('scroll', this.scroll)
        window.addEventListener('resize', this.hideHeader)
        this.colorHeader(this.$route.name)
        this.colorBackground(this.$route.name)

        
        this.fetchMe().then(() => {

            if(this.me && this.me.admin) {
                this.menuItems = [
                    /*{ name: 'Takti', route: 'admin_bars', text: 'ADMIN' },*/
                    { name: 'Generiranje', route: 'admin_players', text: 'ADMIN' },
                ].concat(this.menuItems);
            }

        });

        this.hideHeader()
    },
    beforeDestroy () {
        window.removeEventListener('scroll', this.scroll)
        window.removeEventListener('resize', this.hideHeader)
    },
    computed: {
        ...mapState(['me', 'headerMenuDisabled']),
        userId () {
            return this.user ? this.me.id : 0
        }
    },
    watch: {
        '$route' (to, from) {
            this.colorHeader(to.name)
            this.colorBackground(to.name)
            this.hideHeader()
        }
    },
    methods: {
        ...mapActions(['fetchMe']),
        scroll () {
            this.isHeaderSticky = this.isMenuOpened ? true : (!this.isHeaderHidden && window.pageYOffset > 0)
            this.$emit('sticky-header', this.isHeaderSticky)
        },
        colorHeader (route) {
            this.isHeaderColored = this.uncoloredRoutes.indexOf(route) >= 0
        },
        colorBackground (route) {
            $('body').css('background-image', this.uncoloredRoutes.indexOf(route) >= 0 ? 'none' : this.backgroundImage)
        },
        scroll () {

            if(this.headerMenuDisabled){
                return;
            }

            this.isHeaderSticky = this.isMenuOpened ? true : window.pageYOffset > 0
            this.$emit('sticky-header', this.isHeaderSticky)

        },

        hideHeader () {
            this.isHeaderHidden = window.innerHeight < window.innerWidth && this.$route.path === '/game/intervals'
        },
        toggleMenu (event) {
            if (!this.outsideClick) {
                this.isMenuInitialized = true
                this.isMenuOpened = !this.isMenuOpened
                this.scroll()
                event.stopPropagation()
            }
            this.outsideClick = false
        },
        clickedOutsideMenu (event) {
            if (this.isMenuOpened) {
                this.isMenuOpened = false
                // using this property instead of event.stopPropagation() which does not work for touch events
                this.outsideClick = true
                this.scroll()
            }
        },
        isItemActive (item) {
            return item.route === this.$route.name
        },
        open (event, item) {
            let func;

            switch (item.route) {
                case 'gameTypes':
                    this.$router.push({name: 'gameModes', params: {type: 'rhythm'}});
                    break;
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
                case 'admin_bars':
                    func = this.reroute(item.route);
                    break;
                case 'admin_games':
                    func = this.reroute(item.route);
                break;
                case 'admin_players':
                    func = this.reroute(item.route);
                break;
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
