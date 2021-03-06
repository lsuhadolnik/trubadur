import '../bootstrap'

import VueRouter from 'vue-router'

import Icon from 'vue-awesome/components/Icon'
import App from '../components/App.vue'
import Badges from '../components/profile/Badges.vue'
import Button from '../components/elements/Button.vue'
import Dashboard from '../components/Dashboard.vue'
import GameModes from '../components/GameModes.vue'
import GameStatistics from '../components/GameStatistics.vue'
import GameTypes from '../components/GameTypes.vue'
import HeaderMenu from '../components/HeaderMenu.vue'
import RhythmLevelSelect from '../components/games/rhythm/LevelSelect.vue'

import AdminGames from '../components/admin/AdminGames.vue'
import AdminPlayers from '../components/admin/AdminPlayers.vue'
import AdminRhythmBars from '../components/admin/AdminRhythmBars.vue'
import AdminSettings from '../components/admin/AdminSettings.vue'
import AdminExercise from '../components/admin/AdminExerciseView.vue'
import AdminStatistics from '../components/admin/AdminStatistics.vue'
import AdminUsers from '../components/admin/AdminUsers.vue'
import AdminLeaderboardProjector from '../components/admin/AdminLeaderboardProjector.vue'

import Intervals from '../components/Intervals.vue'
import Rhythm from '../components/games/rhythm/Rhythm.vue'



import PlaybackTest from '../components/PlaybackTest.vue'

import Keyboard from '../components/music/Keyboard.vue'
import Leaderboard from '../components/Leaderboard.vue'
import Levels from '../components/profile/Levels.vue'
import Loader from '../components/elements/Loader.vue'
import Me from '../components/profile/Me.vue'
import Note from '../components/music/Note.vue'
import PageNotFound from '../components/PageNotFound.vue'
import Profile from '../components/Profile.vue'
import Settings from '../components/Settings.vue'
import Stave from '../components/music/Stave.vue'
import Title from '../components/elements/Title.vue'

Vue.component('icon', Icon)
Vue.component('app', App)
Vue.component('element-button', Button)
Vue.component('element-loader', Loader)
Vue.component('element-title', Title)
Vue.component('header-menu', HeaderMenu)
Vue.component('keyboard', Keyboard)
Vue.component('loader', Loader)
Vue.component('note', Note)
Vue.component('page-not-found', PageNotFound)
Vue.component('profile-me', Me)
Vue.component('profile-levels', Levels)
Vue.component('profile-badges', Badges)
Vue.component('stave', Stave)

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
            name: 'rhythm',
            path: '/game/rhythm/:exerciseId?',
            component: Rhythm,
            props: true
        },
        {
            name: 'rhythmLevelSelect',
            path: '/game/level/rhythm',
            component: RhythmLevelSelect,
            props: true
        },
        {
            name: 'playbackTest',
            path: '/playbackTest',
            component: PlaybackTest,
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
            name: 'admin_games',
            path: '/admin/games',
            component: AdminGames,
            props: true
        },
        {
            name: 'admin_bars',
            path: '/admin/bars',
            component: AdminRhythmBars,
            props: true
        },
        {
            name: 'admin_leaderboard_projector',
            path: '/admin/leaderboard',
            component: AdminLeaderboardProjector,
            props: true
        },
        {
            name: 'admin_players',
            path: '/admin/players',
            component: AdminPlayers,
            props: true
        },
        {
            name: 'admin_settings',
            path: '/admin/settings',
            component: AdminSettings,
            props: true
        },
        {
            name: 'admin_exercise',
            path: '/admin/exercise/:id',
            component: AdminExercise,
            props: true
        },
        
        {
            name: 'admin_statistics',
            path: '/admin/statistics',
            component: AdminStatistics,
            props: true
        },
        {
            name: 'admin_users',
            path: '/admin/users',
            component: AdminUsers,
            props: true
        },

        
        {
            name: 'pageNotFound',
            path: '*',
            component: PageNotFound
        }
    ]
})
