import './bootstrap'
import './utils/vueHelpers'

import router from './router'
import store from './store'

new Vue({ // eslint-disable-line no-new
    router,
    el: '#app',
    store
})
