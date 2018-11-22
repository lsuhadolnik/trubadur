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

.dashboard__avatar {
    width  : 120px;
    height : 120px;
}

.dashboard__name {
    width           : 80%;
    padding         : 5px 0;
    text-align      : center;
    font-size       : 25px;
}

.dashboard__level-rating { font-size: 17px; }

.dashboard__label { margin-top: 20px; }
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.dashboard__area .button .button__full { background-color: $sea-green; }
</style>

<template>
    <div class="dashboard">
        <loader v-show="loading"></loader>
        <div class="dashboard__content" v-show="!loading">
            <div class="dashboard__area">
                <img class="dashboard__avatar" id="avatar"/>
                <label class="dashboard__name">{{ name }}</label>
                <label class="dashboard__level-rating">NIVO {{ level }} | {{ rating }}</label>
            </div>
            <div class="dashboard__area">
                <element-button text="igra" @click.native="reroute('gameTypes')"></element-button>
            </div>
            <div class="dashboard__area">
                <element-title text="obvestila"></element-title>
                <label class="dashboard__label">Ni novih obvestil.</label>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    data () {
        return {
            loading: true,
            level: 0
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchMe().then(() => {
                this.fetchLevel({ rating: this.me.rating }).then((level) => {
                    this.level = level.level

                    this.loadImages()
                })
            })
        })
    },
    computed: {
        ...mapState(['me']),
        name () {
            return this.me ? this.me.name : ''
        },
        rating () {
            return this.me ? this.me.rating : 0
        }
    },
    methods: {
        ...mapActions(['fetchMe', 'fetchLevel']),
        loadImages () {
            const context = this

            const avatar = this.$el.querySelector('#avatar')
            avatar.onload = () => { context.loading = false }
            avatar.src = this.me.avatar
        },
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
