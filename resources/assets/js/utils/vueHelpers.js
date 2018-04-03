Vue.filter('capitalize', (value) => value[0].toUpperCase() + value.slice(1))

Vue.filter('uppercase', (value) => value.toUpperCase())

Vue.directive('focus', { inserted: (el) => el.focus() })

Vue.directive('click-outside', {
    bind (el, binding, vnode) {
        this.event = (event) => {
            if (!(el === event.target || el.contains(event.target))) {
                vnode.context[binding.expression](event)
            }
        }
        document.body.addEventListener('click', this.event)
    },
    unbind (el) {
        document.body.removeEventListener('click', this.event)
    }
})
