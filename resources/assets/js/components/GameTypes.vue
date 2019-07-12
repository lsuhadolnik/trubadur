<style lang="scss" scoped>
@import '../../sass/variables/index';

$choose-height: 170px;

.game-types { width: 100%; }

.game-types__content {
    width          : 100%;
    height         : 100%;
    padding-bottom : $bottom-padding;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.game-types__choose {
    width          : 100%;
    height         : $choose-height;
    padding-top    : 20px;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.game-types__label { margin: $label-margin; }

.game-types__arrow {
    width  : 30px;
    height : 30px;
}

.game-types__options {
    width            : 100%;
    min-height       : calc(77vh - #{$header-height} - #{$choose-height} - #{$content-margin} - #{$bottom-padding});
    margin-top       : $content-margin;
    padding          : 20px 0;
    display          : flex;
    align-items      : center;
    justify-content  : center;
    flex-direction   : row;
    background-color : $sunglow;
}

.image-button {
    border: 3px black solid;
    width: 160px;
    height: 200px;
    margin-left: 10px;
    border-radius: 6px;
    cursor: pointer;
}

.image-button__image {
    text-align: center;
    padding: 23px 0 20px 0;
}

.image-button__text {
    text-align: center;
}
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.game-types__choose .title { font-size: 25px; }
.game-types__options .button .button__full { background-color: $golden-tainoi; }
</style>

<template>
    <div class="game-types">
        <loader v-show="loading"></loader>
        <div class="game-types__content" v-show="!loading">
            <div class="game-types__choose">
                <element-title text="izberi igro"></element-title>
                <label class="game-types__label">Izberi področje igre</label>
                <img class="game-types__arrow" id="arrow"/>
            </div>
            <div class="game-types__options">
                <!--<element-button text="Melodični narek" @click.native="reroute('gameModes', { type: 'intervals' })"></element-button>
                <element-button text="Ritmični narek" @click.native="reroute('gameModes', { type: 'rhythm' })"></element-button>-->
                <div class="image-button" @click="reroute('gameModes', { type: 'intervals' })">
                    <div class="image-button__image"><img src="/images/games/game_melody.png" width="100" height="100"></div>
                    <div class="image-button__text">Melodični narek</div>
                </div>
                <div class="image-button" @click="reroute('gameModes', { type: 'rhythm' })">
                    <div class="image-button__image"><img src="/images/games/game_rhythm.png" width="100" height="100"></div>
                    <div class="image-button__text">Ritmični narek</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            loading: true
        }
    },
    mounted () {
        this.$nextTick(() => {
            this.loadImages()
        })
    },
    methods: {
        loadImages () {
            const context = this

            const arrow = this.$el.querySelector('#arrow')
            arrow.onload = () => { context.loading = false }
            arrow.src = '/images/arrows/down.svg'
        },
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
