import '../bootstrap'

import VueRouter from 'vue-router'

import Icon from 'vue-awesome/components/Icon'

import App from '../components/App.vue'
import Dashboard from '../components/Dashboard.vue'
import GameModes from '../components/GameModes.vue'
import GameStatistics from '../components/GameStatistics.vue'
import GameTypes from '../components/GameTypes.vue'
import Intervals from '../components/Intervals.vue'
import Leaderboard from '../components/Leaderboard.vue'
import Profile from '../components/Profile.vue'
import Settings from '../components/Settings.vue'
import Test from '../components/Test.vue'

Vue.component('icon', Icon)
Vue.component('app', App)
Vue.component('dashboard', Dashboard)
Vue.component('game-modes', GameModes)
Vue.component('game-statistics', GameStatistics)
Vue.component('games-types', GameTypes)
Vue.component('intervals', Intervals)
Vue.component('leaderboard', Leaderboard)
Vue.component('profile', Profile)
Vue.component('settings', Settings)
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
            path: '/game/modes',
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
