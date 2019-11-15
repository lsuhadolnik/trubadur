<style lang="scss" scoped>
@import '../../sass/variables/index';

.hugeText {
    font-size: 63px;
}

.text-center {
    text-align: center;
}

.game-statistics { width: 100%; }

.game-statistics__content {
    width          : 100%;
    height         : 100%;
    padding        : 20px 0 $bottom-padding 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.game-statistics__table {
    width           : 100%;
    border-collapse : collapse;
}

.game-statistics__table-row { height: 50px; }

.game-statistics__table-row--header { border-bottom: 1px solid $dolphin; }

.game-statistics__table-row--body {
    border-bottom : 1px solid $dolphin-transparent;
    cursor        : pointer;
    transition    : background-color 0.1s linear;

    &:hover { background-color: $dolphin-transparent; }
}

.game-statistics__table-column {
    padding    : 5px 10px;
    text-align : left;

    &:last-child {
        width      : 25%;
        text-align : right;
    }
}

.game-statistics__avatar {
    width  : 40px;
    height : 40px;
}

.game-statistics__info {
    margin  : 20px 0;
    padding : 10px 0;
}

.game-statistics__line {
    border-bottom : 1px solid $dolphin-transparent;
}

.game-statistics__achievment-wrap {
    @include breakpoint-small-phone-landscape {
        display: flex;
    }
}

.statistics-title {
    @include breakpoint-small-phone-landscape {
        display: none;
    }
}

.game-statistics__achievment-title {
    font-weight: bold;
    text-align: center;
    font-size: 65px;
    margin-top: 27px;

    @include breakpoint-small-phone-portrait {
        font-size: 41px;
    }

    @include breakpoint-small-phone-landscape {
        font-size: 41px;
        margin-left: 25px;
    }
}

.game-statistics__achievment-subtitle {
    text-align: center;
    font-size: 18px;

    @include breakpoint-small-phone-portrait {
        font-size: 17px;
        padding: 0 10px 0 10px;
    }

    @include breakpoint-small-phone-landscape {
        font-size: 15px;
        margin-left: 28px;
    }

}

.game-statistics__achievment-image {
    text-align: center;
    margin-top: 19px;

    @include breakpoint-small-phone {
        margin-top: 0px;
    }
    
}

.game-statistics__achievment-image-img {

    @include breakpoint-small-phone {
        width: 100px;
    }
}

.game-statistics__achievments {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    margin-top: 25px;
    margin-bottom: 42px;



    @include breakpoint-small-phone-landscape {
        margin-top: 0;
    }

    @include breakpoint-small-phone-landscape {
        width: 60%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}

.game-statistics__achievment-title-side {

    @include breakpoint-small-phone-landscape {
        height: 78vh;
        width: 40%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
}

.game-statistics__achievment-badge-title {
    text-align: center;
    font-size: 21px;

    @include breakpoint-small-phone-landscape {
        font-size: 20px;
    }
}

.game-statistics__achievment-badge-description{
    margin-top: 13px;
    text-align: center;

    @include breakpoint-small-phone-landscape {
        font-size: 11px;
        margin-top: 0;
    }
}

.game-statistics__achievment {
    margin-top: 18px;
    display: inline-block;
    width: 260px;
    background: aliceblue;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    margin-right: 12px;

    @include breakpoint-small-phone-portrait {
        margin-top: 0px;
    }

}

.redRow {
    background: $cabaret;
}


</style>

<template>
    <div class="game-statistics">
        <loader v-show="loading"></loader>

        <div class="" v-if="!didNotParticipate">

            <div class="game-statistics__achievment-wrap" v-if="achievments.length > 0">

                <div class="game-statistics__achievment-title-side">
                    <div class="game-statistics__achievment-title">
                        Čestitam!
                    </div>
                    <div class="game-statistics__achievment-subtitle">
                        Napredoval/a si do novega dosežka v igri!
                    </div>
                </div>

                <div class="game-statistics__achievments">
                    <div class="game-statistics__achievment" v-for="a in achievments" :key="a.id">

                        <div class="game-statistics__achievment-image">
                            <img :src="a.image" class="game-statistics__achievment-image-img">
                        </div>

                        <div class="game-statistics__achievment-badge-title">
                            {{a.title}}
                        </div>

                        <div class="game-statistics__achievment-badge-description">
                            {{a.description}}
                        </div>

                    </div>
                </div>  

            </div>

            <div class="game-statistics__content" v-if="!loading">
                
                <sexy-button 
                    :cols="3" 
                    color="green" 
                    style="margin-bottom: 20px;" 
                    @click.native="continueGameType()">Nadaljuj</sexy-button>

                <element-title class="statistics-title" text="Konec igre"></element-title>
                <div class="text-center" style="margin-top: 20px;">
                    <div class="">Dobil/a si</div>
                    <div class="hugeText">{{formatPoints(users[0].points)}} točk</div>
                    <div class="" style="margin-bottom: 21px;">Skupaj imaš {{users[0].rating}} točk</div>
                </div>

                <sexy-button 
                    :cols="3" 
                    color="cabaret" 
                    style="margin-bottom: 20px;" 
                    @click.native="newGame()"> Nova igra</sexy-button>

                <sexy-button 
                    :cols="3" 
                    color="sunglow" 
                    style="margin-bottom: 20px;" 
                    @click.native="gotoLeaderboard()">Poglej lestvico</sexy-button>

                
            </div>
        </div>

        <div class="" v-if="didNotParticipate">
            <h1 class="text-center">Nisi igral/a te igre.</h1>
            <p class="text-center">Žal ne moreš dostopati do teh podatkov.</p>
        </div>
    </div>
</template>

<script>
import { mapActions, mapState } from 'vuex'

import SexyButton from "./elements/SexyButton.vue"

export default {
    props: ['id'],
    components: { SexyButton },
    data () {
        return {
            loading: true,
            users: [],
            statistics: null,

            achievments: [],
            leaderboard: [],
            thisGame: {},

            didNotParticipate: false,
        }
    },
    computed: {
        ...mapState(['me']),
    },
    created () {
        this.fetchMe(true)
            .then(() => { return this.fetchGameStatistics(this.id) })
            .then((data) => {

                if(data.error && data.error == 'DIDNTPARTICIPATE'){
                    
                    this.didNotParticipate = true;
                    this.loading = false;
                    return;
                }

                this.users = data.users
                this.statistics = data.statistics
                this.achievments = data.achievments;
                this.leaderboard = data.leaderboard;
                this.thisGame = data.thisGame;
                
                this.loading = false
        });
    },
    methods: {
        ...mapActions(['fetchGameStatistics', 'storeGame', 'updateGameUser', 'fetchMe']),
        
        newGame() {
            this.$router.push({name:'gameTypes'});
        },

        gotoLeaderboard() {
            this.$router.push({name: 'leaderboard'});
        },

        continueGameType() {

            this.loading = true

            let gameObj = this.thisGame;

            let diff = this.thisGame.difficulty_id || this.thisGame.rhythm_level;
            if(!diff) {
                diff = 1;   
            }


            gameObj.difficulty_id = diff;
            gameObj.rhythm_level = gameObj.rhythm_level ? gameObj.rhythm_level : '0';

            this.storeGame(gameObj).then((game) => {
                this.updateGameUser({ 
                        gameId: game.id, 
                        userId: this.thisGame.users[0], 
                        data: { instrument: this.me.instrument } 
                    }).then(() => {


                        this.reroute(this.thisGame.type, { game: game, difficulty: diff });
                })
            })
        },

        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        },

        getMyScore() {
            
            for(let i = 0; i < this.users.length; i++){
                let user = this.users[i];
                if(user.id == this.me.id) {
                    return points;
                }
            }

            return "Nisi del te igre";

        },

        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        },
        formatPoints (points) {
            if(!points || points == 0) {
                return '0';
            }

            return (points > 0 ? '+' : '') + points
        },
        formatSuccess (success) {
            return success ? 'Pravilno' : 'Nepravilno'
        },
        formatNumber (number, nDecimals = 1) {
            if (number == null) return -1;
            
            return number.toFixed(nDecimals)
        },
        formatTime (time, nDecimals = 1) {
            return this.formatNumber(time / 1000, nDecimals) + 's'
        },
        formatPercent (fraction, nDecimals = 1) {
            return this.formatNumber(fraction * 100, nDecimals) + '%'
        }
    }
}
</script>
