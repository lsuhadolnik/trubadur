import '../bootstrap'

import VueRouter from 'vue-router'

import Icon from 'vue-awesome/components/Icon'

import App from '../components/App.vue'
import Dashboard from '../components/Dashboard.vue'
import Games from '../components/Games.vue'
import Intervals from '../components/Intervals.vue'
import Leaderboard from '../components/Leaderboard.vue'
import Profile from '../components/Profile.vue'
import Settings from '../components/Settings.vue'

Vue.component('icon', Icon)
Vue.component('app', App)
Vue.component('dashboard', Dashboard)
Vue.component('games', Games)
Vue.component('intervals', Intervals)
Vue.component('leaderboard', Leaderboard)
Vue.component('profile', Profile)
Vue.component('settings', Settings)

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        { name: 'dashboard', path: '/home', component: Dashboard },
        { name: 'games', path: '/games', component: Games },
        { name: 'intervals', path: '/intervals', component: Intervals },
        { name: 'leaderboard', path: '/leaderboard', component: Leaderboard },
        { name: 'profile', path: '/profile/:id', component: Profile, props: true },
        { name: 'settings', path: '/settings', component: Settings }
    ]
})
