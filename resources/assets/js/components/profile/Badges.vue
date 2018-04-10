<style lang="scss" scoped>
@import '../../../sass/variables/index';

.badges { width: 100%; }

.badges__content {
    width          : 100%;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.badges__grid {
    width                 : 100%;
    padding               : 20px;
    display               : grid;
    grid-template-columns : repeat(2, 1fr);
    grid-column-gap       : 20px;
    grid-row-gap          : 20px;
    background-color      : $moss-green;
}

.badges__grid--incomplete { padding-top: 0; }

.badges__badge-wrapper {
    grid-column     : span 1;
    display         : flex;
    align-items     : center;
    flex-direction  : column;
}

.badges__badge-image { max-width: 120px; }

.badges__badge-name {
    min-height    : 120px;
    margin-bottom : 10px;
    padding       : 5px 0;
    display       : flex;
    align-items   : center;
    border-bottom : 3px solid $killarney;
    color         : $killarney;
    font-size     : 18px;
    text-align    : center;
}

.badges__badge-description { text-align: center; }
</style>

<template>
    <div class="badges">
        <loader v-show="loading"></loader>
        <div class="badges__content" v-show="!loading">
            <div class="badges__grid">
                <div class="badges__badge-wrapper" v-for="(badge, index) in completedBadges">
                    <img class="badges__badge-image" :id="'completed_badge_' + index"/>
                    <div class="badges__badge-name">{{ badge.name | uppercase }}</div>
                    <div class="badges__badge-description">{{ badge.description }}</div>
                </div>
            </div>
            <div class="badges__grid badges__grid--incomplete">
                <div class="badges__badge-wrapper" v-for="(badge, index) in incompleteBadges">
                    <img class="badges__badge-image" :id="'incomplete_badge_' + index"/>
                    <div class="badges__badge-name">{{ badge.name | uppercase }}</div>
                    <div class="badges__badge-description">{{ badge.description }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'

export default {
    props: ['id'],
    data () {
        return {
            loading: true,
            user: null,
            userBadges: []
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchMe().then(() => {
                this.fetchUser(this.id).then((user) => {
                    this.user = user

                    this.fetchUserBadges({ per_page: 0, order_by: 'completed,updated_at', order_direction: 'desc,asc', filter_user_id: this.user.id }).then((userBadges) => {
                        this.userBadges = userBadges

                        this.$nextTick(() => {
                            this.loadImages()
                        })
                    })
                })
            })
        })
    },
    computed: {
        ...mapState(['me']),
        completedBadges () {
            return this.userBadges.length > 0 ? this.userBadges.filter(userBadge => userBadge.completed).map(userBadge => userBadge.badge) : []
        },
        incompleteBadges () {
            return this.userBadges.length > 0 ? this.userBadges.filter(userBadge => !userBadge.completed).map(userBadge => userBadge.badge) : []
        }
    },
    methods: {
        ...mapActions(['fetchMe', 'fetchUser', 'fetchUserBadges']),
        loadImages () {
            const context = this

            let nLoaded = 0
            const nTotal = this.userBadges.length

            for (let i = 0; i < this.completedBadges.length; i++) {
                const badge = this.$el.querySelector('#completed_badge_' + i)
                badge.onload = () => {
                    if (++nLoaded === nTotal) {
                        context.loading = false
                    }
                }
                badge.src = this.completedBadges[i].image
            }

            for (let i = 0; i < this.incompleteBadges.length; i++) {
                const badge = this.$el.querySelector('#incomplete_badge_' + i)
                badge.onload = () => {
                    if (++nLoaded === nTotal) {
                        context.loading = false
                    }
                }
                badge.src = '/images/badges/locked.svg'
            }
        }
    }
}
</script>
