<style lang="scss" scoped>
@import '../../sass/variables/index';

$instructions-height: 290px;

.leaderboard { width: 100%; }

.leaderboard__content {
    width          : 100%;
    height         : 100%;
    padding-bottom : $bottom-padding;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.leaderboard__instructions {
    width          : 100%;
    height         : $instructions-height;
    padding-top    : 10px;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.leaderboard__image {
    width         : 120px;
    height        : 120px;
    margin-bottom : 10px;
}

.leaderboard__label { margin: $label-margin; }

.leaderboard__arrow {
    width  : 30px;
    height : 30px;
}

.leaderboard__table {
    width           : 100%;
    margin-top      : $content-margin;
    border-collapse : collapse;
}

.leaderboard__table-row--header {
    height           : 30px;
    background-color : $neptune;
    color            : $spectra;
}

.leaderboard__table-row--body {
    height : 65px;
    cursor : pointer;

    &:nth-last-child(2n + 1) { background-color: $skeptic; }
    &:nth-last-child(2n)     { background-color: $neptune; }
}

.leaderboard__table-column {
    padding    : 5px 10px;
    text-align : left;

    &:last-child { text-align : right; }
}

.leaderboard__table-column--1 { width : 10%; }
.leaderboard__table-column--2 { width : 20%; }
.leaderboard__table-column--3 { width : 40%; }
.leaderboard__table-column--4 { width : 20%; }

.leaderboard__avatar {
    width  : 50px;
    height : 50px;
}
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.leaderboard__instructions .title { font-size: 25px; }
</style>

<template>
    <div class="leaderboard">
        <loader v-show="loading"></loader>
        <div class="leaderboard__content" v-show="!loading">
            <div class="leaderboard__instructions">
                <img class="leaderboard__image" id="image"/>
                <element-title text="lestvica"></element-title>
                <label class="leaderboard__label">Poglej, kako visoko se uvrščaš.</label>
                <img class="leaderboard__arrow" id="arrow"/>
            </div>
            <table class="leaderboard__table">
                <thead>
                    <tr class="leaderboard__table-row leaderboard__table-row--header">
                        <th class="leaderboard__table-column leaderboard__table-column--header" colspan="2">MESTO</th>
                        <th class="leaderboard__table-column leaderboard__table-column--header" colspan="1">IME</th>
                        <th class="leaderboard__table-column leaderboard__table-column--header" colspan="1">TOČKE</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="leaderboard__table-row leaderboard__table-row--body" @click="reroute('profile', { id: user.id })" v-for="(user, index) in users">
                        <td class="leaderboard__table-column leaderboard__table-column--body leaderboard__table-column--1">{{ index + 1 }}</td>
                        <td class="leaderboard__table-column leaderboard__table-column--body leaderboard__table-column--2">
                            <img class="leaderboard__avatar" :id="'avatar_' + user.id"/>
                        </td>
                        <td class="leaderboard__table-column leaderboard__table-column--body leaderboard__table-column--3">{{ user.name }}</td>
                        <td class="leaderboard__table-column leaderboard__table-column--body leaderboard__table-column--4">{{ user.rating }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { mapActions } from 'vuex'

export default {
    data () {
        return {
            loading: true,
            users: []
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchUsers({ per_page: 10, fields: 'id,name,rating,avatar', order_by: 'rating', order_direction: 'desc' }).then((users) => {
                this.users = users.data

                this.$nextTick(() => {
                    const context = this

                    let nLoaded = 0

                    const image = this.$el.querySelector('#image')
                    image.onload = () => {
                        if (++nLoaded === context.users.length + 2) {
                            context.loading = false
                        }
                    }
                    image.src = '/images/leaderboard/leaderboard.svg'

                    const arrow = this.$el.querySelector('#arrow')
                    arrow.onload = () => {
                        if (++nLoaded === context.users.length + 2) {
                            context.loading = false
                        }
                    }
                    arrow.src = '/images/arrows/down.svg'

                    for (let user of this.users) {
                        let avatar = this.$el.querySelector('#avatar_' + user.id)
                        avatar.onload = () => {
                            if (++nLoaded === context.users.length + 2) {
                                context.loading = false
                            }
                        }
                        avatar.src = user.avatar
                    }
                })
            })
        })
    },
    methods: {
        ...mapActions(['fetchUsers']),
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
