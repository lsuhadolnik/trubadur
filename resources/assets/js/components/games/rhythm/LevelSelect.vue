<style lang="scss" scoped>
@import '../../../../sass/variables/index';

.game-modes { width: 100%; }

.game-modes__content {
    width          : 100%;
    height         : calc(100vh - #{$header-height});
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.game-modes__items-wrap {
    display: flex;
    flex-direction: column;
    border: 6px rgba(0,0,0,0.1) solid;
    border-radius: 9px;
}

.game-modes__item-row {
    display: flex;
    flex-direction: row;
}

.game-modes__item {
    width           : 100%;
    padding         : 20px;
    flex            : 1;
    display         : flex;
    align-items     : center;
    justify-content : space-between;

    @include breakpoint-tablet { padding: 40px; }

}

.game-modes__item {
    width: 65px;
    height: 65px;

    // &:nth-child(odd)  { background-color: $golden-tainoi; }
    // &:nth-child(even) { background-color: $sunglow;       }
}

.game-modes__items-wrap:nth-child(even) .game-modes__item-row:nth-child(even){
    background-color: $golden-tainoi;
}

.game-modes__items-wrap:nth-child(even) .game-modes__item-row:nth-child(odd){
    background-color: $sunglow;
}

.game-modes__items-wrap:nth-child(odd) .game-modes__item-row:nth-child(even){
    background-color: $golden-tainoi;
}

.game-modes__items-wrap:nth-child(odd) .game-modes__item-row:nth-child(odd){
    background-color: $sunglow;
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

.selected {
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 12px;
    border-radius: 41px;
    width: 40px;
    height: 40px;
}
</style>

<template>
    <div class="game-modes">
        <loader v-show="loading"></loader>
        <div class="game-modes__content" v-show="!loading">
            <h1 class="game-modes__title">Izberi nivo</h1>
            <div class="game-modes__items-wrap">
                <div class="game-modes__item-row" v-for="grade in levels" :key="grade[0]">
                    <div class="game-modes__item" v-for="level in grade" :key="level" @click="selectLevel(level)">
                        <span v-if="level <= userLevel" class="game-modes__item-text" :class="{selected: level == selectedLevel}">{{level}}</span>
                        <icon v-else name="lock" scale="2" />
                    </div>
                </div>
            </div>
            <SexyButton style="margin-top: 17px;" text="Nadaljuj" @click.native="createGame()" :cols="2" color="green"/>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

import 'vue-awesome/icons/lock'
import SexyButton from '../../elements/SexyButton.vue'

export default {
    props: ['type', 'mode'],
    data () {
        return {
            loading: true,
            userLevel: 11,
            selectedLevel: 11,
            levels: [
                [11, 12, 13, 14],
                [21, 22, 23, 24],
                [31, 32, 33, 34],
                [41, 42, 43, 44],
            ]
        }
    },
    components: {
        SexyButton
    },
    mounted () {

        if(!this.type || !this.mode) {
            this.$router.push({ name: 'dashboard' })
        }

        this.$nextTick(() => {
            this.fetchMe().then(() => {
                this.userLevel = this.me.rhythm_level;
                //this.userLevel = 14;
                
                this.selectedLevel = this.userLevel;
                this.loading = false;
            })
        })
    },
    computed: {
        ...mapState(['me'])
    },
    methods: {
        ...mapActions(['fetchMe', 'fetchDifficulty', 'fetchRhythmDifficulty', 'storeGame', 'updateGameUser']),
        
        selectLevel(level) {

            if(level <= this.userLevel){
                this.selectedLevel = level;
            }

        },

        createGame () {

            this.loading = true
              
            const users = [this.me.id];
            let gameObj = { mode: this.mode, type: this.type, users: users, rhythm_level: this.selectedLevel };

            this.storeGame(gameObj).then((game) => {
                this.updateGameUser({ gameId: game.id, userId: this.me.id, data: { instrument: this.me.instrument } }).then(() => {
                    this.loading = false
                    this.reroute(this.type, { game: game, difficulty: this.selectedLevel })
                })
            })
        },
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
