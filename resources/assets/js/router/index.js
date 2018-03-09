import '../bootstrap'

import VueRouter from 'vue-router'

import Icon from 'vue-awesome/components/Icon'
import App from '../components/App.vue'
import Dashboard from '../components/Dashboard.vue'
// import Settings from '../components/Settings.vue'
import Intervals from '../components/Intervals.vue'

Vue.component('icon', Icon)
Vue.component('app', App)
Vue.component('dashboard', Dashboard)
// Vue.component('settings', Settings)
Vue.component('intervals', Intervals)

Vue.use(VueRouter)

export default new VueRouter({
    mode: 'history',
    routes: [
        { name: 'dashboard', path: '/home', component: Dashboard },
        // { name: 'settings', path: '/settings', component: Settings },
        { name: 'intervals', path: '/intervals', component: Intervals }
    ]
})
