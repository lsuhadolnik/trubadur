import VueRouter from 'vue-router'
import Vuex from 'vuex'

import './bootstrap'
import './utils/vueHelpers'

import App from './components/App.vue'
import Dashboard from './components/Dashboard.vue'

Vue.component('app', App)
Vue.component('dashboard', Dashboard)

Vue.use(VueRouter)

const router = new VueRouter({
    mode: 'history',
    routes: [
        { name: 'dashboard', path: '/home', component: Dashboard }
    ]
})

const store = new Vuex.Store({
    state: { },
    getters: { },
    mutations: { }
})

new Vue({ // eslint-disable-line no-new
    router,
    el: '#app',
    store
})
