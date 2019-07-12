<style lang="scss" scoped>
@import '../../sass/variables/index';

.game-modes { width: 100%; }

.game-modes__content {
    width          : 100%;
    height         : calc(100vh - #{$header-height});
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.game-modes__item {
    width           : 100%;
    padding         : 20px;
    flex            : 1;
    display         : flex;
    align-items     : center;
    justify-content : space-between;

    @include breakpoint-tablet { padding: 40px; }

    &:nth-child(odd)  { background-color: $golden-tainoi; }
    &:nth-child(even) { background-color: $sunglow;       }
}

.game-modes__image {
    width  : 100px;
    height : 100px;

    @include breakpoint-tablet {
        width  : 150px;
        height : 150px;
    }

    @include breakpoint-large-phone-landscape {
        width  : 85px;
        height : 85px;
    }

    @include breakpoint-phone-landscape {
        width  : 85px;
        height : 85px;
    }

    @include breakpoint-small-phone-landscape {
        width  : 70px;
        height : 70px;
    }
}

.game-modes__label {
    font-size  : 20px;
    text-align : center;

    @include breakpoint-tablet { font-size: 40px; }
}

.game-modes__arrow {
    width  : 30px;
    height : 30px;

    @include breakpoint-tablet {
        width  : 50px;
        height : 50px;
    }
}
</style>

<template>
    <div class="game-modes">
        <loader v-show="loading"></loader>
        <div class="game-modes__content" v-show="!loading">
            <div class="game-modes__item" v-for="mode in modes" @click="createGame(mode.mode)">
                <img class="game-modes__image" :id="'image_' + mode.mode"/>
                <label class="game-modes__label">{{ mode.name | uppercase }}</label>
                <img class="game-modes__arrow" :id="'arrow_' + mode.mode"/>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    props: ['type'],
    data () {
        return {
            loading: true,
            modes: [
                { name: 'vaja', mode: 'practice', image: 'practice' },
                { name: '1 igralec', mode: 'single', image: 'single' },
                { name: 'več igralcev', mode: 'multi', image: 'multi' }
            ]
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchMe().then(() => {
                this.loadImages()
            })
        })
    },
    computed: {
        ...mapState(['me'])
    },
    methods: {
        ...mapActions(['fetchMe', 'fetchDifficulty', 'storeGame', 'updateGameUser']),
        loadImages () {
            const context = this

            let nLoaded = 0
            const nTotal = this.modes.length * 2

            for (let mode of this.modes) {
                const arrow = this.$el.querySelector('#arrow_' + mode.image)
                arrow.onload = () => {
                    if (++nLoaded === nTotal) {
                        context.loading = false
                    }
                }
                arrow.src = '/images/arrows/right.svg'

                let image = this.$el.querySelector('#image_' + mode.image)
                image.onload = () => {
                    if (++nLoaded === nTotal) {
                        context.loading = false
                    }
                }
                image.src = '/images/games/' + mode.image + '.svg'
            }
        },
        createGame (mode) {

            this.loading = true

            this.fetchDifficulty({ gradeId: this.me.grade_id, schoolId: this.me.school_id }).then((difficulty) => {
                const users = []
                switch (mode) {
                    case 'practice':
                    case 'single':
                        users.push(this.me.id)
                        break
                    // TODO: create lobby etc.
                    case 'multi':
                        users.push(this.me.id)
                        break
                }

                this.storeGame({ difficulty_id: difficulty.id, mode: mode, type: this.type, users: users }).then((game) => {
                    this.updateGameUser({ gameId: game.id, userId: this.me.id, data: { instrument: this.me.instrument } }).then(() => {
                        this.loading = false
                        //this.reroute('intervals', { game: game, difficulty: difficulty })
                        this.reroute(this.type, { game: game, difficulty: difficulty })
                    })
                })
            })
        },
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
