<style lang="scss" scoped>
@import '../../sass/app';

.game-modes {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}

.game-modes__loader {
    position  : absolute;
    top       : 50%;
    left      : 50%;
    transform : translate(-50%, -50%);
    width     : 100px;
    height    : 100px;

    @include breakpoint-phone {
        width  : 75px;
        height : 75px;
    }
}

.game-modes__command-wrapper {
    padding        : 20px 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.game-modes__command {
    width                 : calc(100vw / 3);
    height                : 50px;
    margin                : 10px 0;
    padding-top           : 5px;
    display               : flex;
    justify-content       : center;
    align-items           : center;
    font-size             : 20px;
    font-family           : $font-title;
    background-color      : $blue;
    color                 : $black;
    opacity               : 0.8;
    -webkit-touch-callout : none;
    -webkit-user-select   : none;
    -khtml-user-select    : none;
    -moz-user-select      : none;
    -ms-user-select       : none;
    user-select           : none;
    cursor                : pointer;
    transition            : opacity 0.1s linear;

    @include breakpoint-tablet { opacity : 1; }
    @include breakpoint-phone  { opacity : 1; }

    &:hover { opacity: 1; }
}
</style>

<template>
    <div class="game-modes">
        <img class="game-modes__loader" src="/images/loader.svg" v-show="loading"/>
        <div class="game-modes__command-wrapper" v-show="!loading">
            <div class="game-modes__command" @click="createGame('practice')">Vaja</div>
            <div class="game-modes__command" @click="createGame('single')">En igralec</div>
            <!-- <div class="game-modes__command" @click="createGame('multi')">Več igralcev</div> -->
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    props: ['type'],
    data () {
        return {
            loading: true
        }
    },
    created () {
        if (!this.type) {
            this.$router.push({ name: 'dashboard' })
        } else {
            this.fetchMe().then(() => {
                this.loading = false
            })
        }
    },
    computed: {
        ...mapState(['me'])
    },
    methods: {
        ...mapActions(['fetchMe', 'fetchLevel', 'storeGame']),
        createGame (mode) {
            this.fetchLevel({ gradeId: this.me.grade_id, schoolId: this.me.school_id }).then((level) => {
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

                this.storeGame({ level_id: level.id, mode: mode, type: this.type, users: users }).then((game) => {
                    this.open('intervals', { game: game })
                })
            })
        },
        open (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
