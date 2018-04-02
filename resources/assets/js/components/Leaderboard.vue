<style lang="scss" scoped>
@import '../../sass/variables/index';

.leaderboard {
    width            : 100%;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
}

.leaderboard__loader {
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

.leaderboard__title {
    font-family : $font-regular;
    font-size   : 30px;
    font-weight : bold;
}

.leaderboard__table {
    width           : 100%;
    border-collapse : collapse;
}

.leaderboard__table-row { height: 50px; }

.leaderboard__table-row--header { border-bottom: 1px solid $dolphin; }

.leaderboard__table-row--body {
    border-bottom : 1px solid $dolphin-transparent;
    cursor        : pointer;
    transition    : background-color 0.1s linear;

    &:hover { background-color: $dolphin-transparent; }
}

.leaderboard__table-column {
    padding    : 5px 10px;
    text-align : left;

    &:last-child {
        width      : 25%;
        text-align : right;
    }
}

.leaderboard__avatar {
    width  : 40px;
    height : 40px;
}
</style>

<template>
    <div class="leaderboard">
        <div class="leaderboard__title">{{ title | uppercase }}</div>
        <img class="leaderboard__loader" src="/images/loader.svg" v-show="loading"/>
        <div v-show="!loading">
            <table class="leaderboard__table">
                <thead>
                    <tr class="leaderboard__table-row leaderboard__table-row--header">
                        <th class="leaderboard__table-column leaderboard__table-column--header"></th>
                        <th class="leaderboard__table-column leaderboard__table-column--header">Ime</th>
                        <th class="leaderboard__table-column leaderboard__table-column--header">Št. točk</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="leaderboard__table-row leaderboard__table-row--body" @click="open('profile', { id: user.id })" v-for="user in users">
                        <td class="leaderboard__table-column leaderboard__table-column--body">
                            <img class="leaderboard__avatar" :id="'avatar_' + user.id"/>
                        </td>
                        <td class="leaderboard__table-column leaderboard__table-column--body">{{ user.name }}</td>
                        <td class="leaderboard__table-column leaderboard__table-column--body">{{ user.rating }}</td>
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
            title: 'Lestvica',
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
                    for (let user of this.users) {
                        let avatar = this.$el.querySelector('#avatar_' + user.id)
                        avatar.onload = () => {
                            if (++nLoaded === context.users.length) {
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
        open (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
