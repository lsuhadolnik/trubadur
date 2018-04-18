<style lang="scss" scoped>
@import '../../../sass/variables/index';

.levels { width: 100%; }

.levels__content {
    width            : 100%;
    padding          : 20px 0;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $skeptic;
}

.levels__area {
    width   : 100%;
    padding : 0 5%;
}

.levels__level {
    display         : flex;
    align-items     : center;
    justify-content : center;
}

.levels__label-wrapper { display: flex; }

.levels__label-wrapper--level {
    width           : 20%;
    padding-right   : 10px;
    justify-content : flex-end;
}

.levels__label-wrapper--label {
    width           : 45%;
    padding-left    : 10px;
    justify-content : flex-start;
}

.levels__label { font-size: 13px; }

.levels__label--level { text-align: right; }
.levels__label--label { text-align: left;  }

.levels__image-wrapper {
    width           : 35%;
    max-width       : 120px;
    display         : flex;
    align-items     : center;
    justify-content : center;
}

.levels__image {
    width  : 100%;
    height : 100%;
}

.levels__line-wrapper {
    width          : 35%;
    height         : 100px;
    margin-left    : 20%;
    vertical-align : top;
}

.levels__line {
    stroke       : $black;
    stroke-width : 5px;
}
</style>

<template>
    <div class="levels">
        <loader v-show="loading"></loader>
        <div class="levels__content" v-show="!loading">
            <div class="levels__area" v-for="level in levelsWithLabel">
                <div class="levels__level">
                    <div class="levels__label-wrapper levels__label-wrapper--level">
                        <label class="levels__label levels__label--level">NIVO {{ level.level }}</label>
                    </div>
                    <div class="levels__image-wrapper">
                        <img class="levels__image" :id="'level_' + level.level"/>
                    </div>
                    <div class="levels__label-wrapper levels__label-wrapper--label">
                        <label class="levels__label levels__label--label">{{ level.label | uppercase }}</label>
                    </div>
                </div>
                <svg class="levels__line-wrapper" v-if="level.id !== levelsWithLabel[levelsWithLabel.length - 1].id">
                    <line class="levels__line" x1="50%" y1="0" x2="50%" y2="100%"/>
                </svg>
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
            loading: true,
            user: null,
            levels: []
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.fetchUser(this.id).then((user) => {
                this.user = user

                this.fetchLevels({ per_page: 0 }).then((levels) => {
                    this.levels = levels

                    this.$nextTick(() => {
                        this.loadImages()
                    })
                })
            })
        })
    },
    computed: {
        levelsWithLabel () {
            return this.levels.length > 0 ? this.levels.filter(level => level.label !== null) : []
        }
    },
    methods: {
        ...mapActions(['fetchUser', 'fetchLevels']),
        loadImages () {
            const context = this

            let nLoaded = 0
            const nTotal = this.levelsWithLabel.length

            for (let level of this.levelsWithLabel) {
                const image = this.$el.querySelector('#level_' + level.level)
                image.onload = () => {
                    if (++nLoaded === nTotal) {
                        context.loading = false
                    }
                }
                image.src = this.user.rating >= level.min_rating ? level.image : '/images/levels/locked.svg'
            }
        }
    }
}
</script>
