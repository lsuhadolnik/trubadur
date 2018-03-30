<style lang="scss" scoped>
@import '../../sass/app';

.leaderboard {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}
</style>

<template>
    <div class="leaderboard">
        LESTVICA
        <svg id="timer" width="500" height="500"></svg>
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
    data () {
        return {
            title: 'Lestvica',
            loading: true
        }
    },
    created () {
        // this.fetchUsers().then((users) => {
        //     this.loading = false
        // })
    },
    mounted () {
        this.$nextTick(() => {
            const timer = new TimerProgress({ // eslint-disable-line no-undef
                'container': this.$el.querySelector('#timer'),
                'stroke-width': 60,
                'color-container': '#2c2c2c',
                'color-circle': '#c4c3c3',
                'color-path': '#5e5e5e',
                'color-text': '#c4c3c3',
                'color-alert': '#f05153',
                'font-size': 100,
                'font-family': 'Bebas Neue'
            })

            timer.run(10000, 5000, true, 1)
            setTimeout(() => timer.reset(), 12000)
        })
    },
    methods: {
        ...mapActions(['fetchUsers'])
    }
}
</script>
