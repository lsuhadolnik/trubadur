import '../bootstrap'

import VueRouter from 'vue-router'

import Icon from 'vue-awesome/components/Icon'

import App from '../components/App.vue'
import Button from '../components/elements/Button.vue'
import Dashboard from '../components/Dashboard.vue'
import GameModes from '../components/GameModes.vue'
import GameStatistics from '../components/GameStatistics.vue'
import GameTypes from '../components/GameTypes.vue'
import HeaderMenu from '../components/HeaderMenu.vue'
import Intervals from '../components/Intervals.vue'
import Keyboard from '../components/music/Keyboard.vue'
import Leaderboard from '../components/Leaderboard.vue'
import Loader from '../components/elements/Loader.vue'
import Note from '../components/music/Note.vue'
import Profile from '../components/Profile.vue'
import Settings from '../components/Settings.vue'
import Stave from '../components/music/Stave.vue'
import Title from '../components/elements/Title.vue'
import Test from '../components/Test.vue'

Vue.component('icon', Icon)
Vue.component('app', App)
Vue.component('dashboard', Dashboard)
Vue.component('element-button', Button)
Vue.component('element-loader', Loader)
Vue.component('element-title', Title)
Vue.component('game-modes', GameModes)
Vue.component('game-statistics', GameStatistics)
Vue.component('games-types', GameTypes)
Vue.component('header-menu', HeaderMenu)
Vue.component('intervals', Intervals)
Vue.component('keyboard', Keyboard)
Vue.component('leaderboard', Leaderboard)
Vue.component('Loader', Loader)
Vue.component('note', Note)
Vue.component('settings', Settings)
Vue.component('stave', Stave)
Vue.component('test', Test)

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        {
            name: 'dashboard',
            path: '/home',
            component: Dashboard
        },
        {
            name: 'gameTypes',
            path: '/game/types',
            component: GameTypes
        },
        {
            name: 'gameModes',
            path: '/game/:type/modes',
            component: GameModes,
            props: true
        },
        {
            name: 'intervals',
            path: '/game/intervals',
            component: Intervals,
            props: true
        },
        {
            name: 'gameStatistics',
            path: '/game/:id/statistics',
            component: GameStatistics,
            props: true
        },
        {
            name: 'leaderboard',
            path: '/leaderboard',
            component: Leaderboard
        },
        {
            name: 'profile',
            path: '/profile/:id',
            component: Profile,
            props: true
        },
        {
            name: 'settings',
            path: '/settings',
            component: Settings
        },
        {
            name: 'test',
            path: '/test',
            component: Test,
            props: true
        }
    ]
})
