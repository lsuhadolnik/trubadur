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
    methods: {
        ...mapActions(['fetchUsers'])
    }
}
</script>
