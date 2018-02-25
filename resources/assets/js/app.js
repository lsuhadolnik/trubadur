import VueRouter from 'vue-router'
import Vuex from 'vuex'

import './bootstrap'
import './utils/vueHelpers'

import Icon from 'vue-awesome/components/Icon'
import App from './components/App.vue'
import Dashboard from './components/Dashboard.vue'
import Intervals from './components/Intervals.vue'

Vue.component('icon', Icon)
Vue.component('app', App)
Vue.component('dashboard', Dashboard)
Vue.component('intervals', Intervals)

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        { name: 'dashboard', path: '/home', component: Dashboard },
        { name: 'intervals', path: '/intervals', component: Intervals, props: true }
    ]
})

const store = new Vuex.Store({
    state: {
        midi: null
    },
    getters: {
        midi: state => state.midi
    },
    mutations: {
        setMidi: (state, midi) => {
            state.midi = midi
        }
    }
})

new Vue({ // eslint-disable-line no-new
    router,
    el: '#app',
    store
})
