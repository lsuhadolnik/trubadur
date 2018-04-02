<style lang="scss" scoped>
@import '../../sass/variables/index';

.dashboard { width: 100%; }

.dashboard__content {
    width          : 100%;
    padding        : 20px 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.dashboard__area {
    width          : 100%;
    padding-bottom : 50px;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.dashboard__name {
    width           : 80%;
    padding         : 5px 0;
    display         : flex;
    justify-content : center;
    font-size       : 25px;
}

.dashboard__level-rating { font-size: 17px; }

.dashboard__notification-label { margin-top: 20px; }
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.button .button__full { background-color: $sea-green !important; }
</style>

<template>
    <div class="dashboard">
        <loader v-show="loading"></loader>
        <div class="dashboard__content" v-show="!loading">
            <div class="dashboard__area">
                <img class="dashboard__avatar" id="avatar"/>
                <div class="dashboard__name">{{ name }}</div>
                <div class="dashboard__level-rating">NIVO {{ level }} | {{ rating }}</div>
            </div>
            <div class="dashboard__area">
                <element-button text="igra" @click.native="reroute('gameTypes')"></element-button>
            </div>
            <div class="dashboard__area">
                <element-title text="obvestila"></element-title>
                <label class="dashboard__notification-label">Ni novih obvestil.</label>
            </div>
        </div>

    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    data () {
        return {
            loading: true
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchMe().then(() => {
                const avatar = this.$el.querySelector('#avatar')
                const context = this
                avatar.onload = () => { context.loading = false }
                avatar.src = this.me.avatar
            })
        })
    },
    computed: {
        ...mapState(['me']),
        name () {
            return this.me ? this.me.name : ''
        },
        level () {
            return this.me ? '1' : '' // TODO: add levels table to the database (refactor levels -> difficulties)
        },
        rating () {
            return this.me ? this.me.rating : 0
        }
    },
    methods: {
        ...mapActions(['fetchMe']),
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
