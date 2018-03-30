<style lang="scss" scoped>
@import '../../sass/app';

.test {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}
</style>

<template>
    <div class="test">
        TEST
        <div>{{ name }}</div>
    </div>
</template>

<script>
export default {
    props: ['me'],
    created () {
        if (!this.me) {
            this.$router.push({ name: 'dashboard' })
        }
    },
    computed: {
        name () {
            return this.me ? this.me.name : ''
        }
    }
}
</script>
