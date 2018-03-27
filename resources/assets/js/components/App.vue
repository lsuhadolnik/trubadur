<style lang="scss" scoped>
@import '../../sass/app';

.app {
    width  : 100%;
    height : 100%;
}
</style>

<template>
    <div class="app">
        <nav-header></nav-header>
        <router-view></router-view>
    </div>
</template>

<script>
import NavHeader from './NavHeader.vue'

export default {
    components: {
        navHeader: NavHeader
    }
}
</script>
