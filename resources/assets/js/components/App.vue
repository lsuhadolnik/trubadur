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
import { mapActions } from 'vuex'

export default {
    created () {
        this.fetchMe().then(() => { })
    },
    methods: {
        ...mapActions(['fetchMe'])
    },
    components: {
        navHeader: NavHeader
    }
}
</script>
