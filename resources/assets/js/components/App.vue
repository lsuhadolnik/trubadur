<style lang="scss" scoped>
@import '../../sass/variables/index';

.app {
    width  : 100%;
    height : 100%;
}

.app--sticky { padding-top: $header-height; }
</style>

<template>
    <div class="app">
        <header-menu @sticky-header="setStickyHeader"></header-menu>
        <router-view :class="{ 'app--sticky': isHeaderSticky }"></router-view>
    </div>
</template>

<script>
export default {
    data () {
        return {
            isHeaderSticky: false
        }
    },
    methods: {
        setStickyHeader (value) {
            this.isHeaderSticky = value
        }
    }
}
</script>
