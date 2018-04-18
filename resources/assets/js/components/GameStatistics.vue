<style lang="scss" scoped>
@import '../../sass/variables/index';

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
</style>

<template>
    <div class="game-statistics">
        <loader v-show="loading"></loader>
        <div class="game-statistics__content" v-if="!loading">
            <element-title text="statistika"></element-title>
            <table class="game-statistics__table">
                <thead>
                    <tr class="game-statistics__table-row game-statistics__table-row--header">
                        <th class="game-statistics__table-column game-statistics__table-column--header">#</th>
                        <th class="game-statistics__table-column game-statistics__table-column--header"></th>
                        <th class="game-statistics__table-column game-statistics__table-column--header">Ime</th>
                        <th class="game-statistics__table-column game-statistics__table-column--header">Št. točk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="game-statistics__table-row game-statistics__table-row--body" @click="open('profile', { id: user.id })" v-for="(user, index) in users">
                        <td class="game-statistics__table-column game-statistics__table-column--body">{{ index + 1 }}</td>
                        <td class="game-statistics__table-column game-statistics__table-column--body">
                            <img class="game-statistics__avatar" :src="user.avatar"/>
                        </td>
                        <td class="game-statistics__table-column game-statistics__table-column--body">{{ user.name }}</td>
                        <td class="game-statistics__table-column game-statistics__table-column--body">{{ formatPoints(user.points) }}</td>
                    </tr>
                </tbody>
            </table>
            <div class="game-statistics__info game-statistics__line">
                <strong>Statistika odgovorov:</strong>
            </div>
            <ul class="game-statistics__answer-list">
                <li><i>Povprečen čas:</i> {{ formatTime(statistics.timeAvg) }}</li>
                <li><i>Povprečno št. dodanih not:</i> {{ formatNumber(statistics.nAdditionsAvg) }}</li>
                <li><i>Povprečno št. izbrisanih not:</i> {{ formatNumber(statistics.nDeletionsAvg) }}</li>
                <li><i>Povprečno št. predvajanj:</i> {{ formatNumber(statistics.nPlaybacksAvg) }}</li>
            </ul>
            <div class="game-statistics__chapter" v-for="n in 3">
                <div class="game-statistics__info game-statistics__line">
                    <strong>Poglavje {{ '1 + ' + (n + 2) }}:</strong>
                </div>
                <ul class="game-statistics__success-list">
                    <li v-for="(success, index) in statistics.successByChapter[n]">
                        {{ index + 1 }}. {{ formatSuccess(success) }}
                    </li>
                </ul>
                <div class="game-statistics__success-chapter">
                    <i>Povprečje</i>: {{ formatPercent(statistics.successAvgByChapter[n]) }}
                </div>
            </div>
            <div class="game-statistics__info">
                <strong>Skupno povprečje</strong>: {{ formatPercent(statistics.successAvg) }}
            </div>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
    props: ['id'],
    data () {
        return {
            title: 'Statistika igre',
            loading: true,
            users: [],
            statistics: null
        }
    },
    created () {
        this.fetchGameStatistics(this.id).then((data) => {
            this.users = data.users
            this.statistics = data.statistics
            this.loading = false
        })
    },
    methods: {
        ...mapActions(['fetchGameStatistics']),
        open (name, params = {}) {
            this.$router.push({ name: name, params: params })
        },
        formatPoints (points) {
            return (points > 0 ? '+' : '') + points
        },
        formatSuccess (success) {
            return success ? 'Pravilno' : 'Nepravilno'
        },
        formatNumber (number, nDecimals = 1) {
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
