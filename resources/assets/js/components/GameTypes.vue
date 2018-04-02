<style lang="scss" scoped>
@import '../../sass/variables/index';

$choose-height: 170px;

.game-types { width: 100%; }

.game-types__content {
    width          : 100%;
    height         : 100%;
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

.game-types__label { margin: 20px 0 5px 0; }

.game-types__arrow {
    width  : 30px;
    height : 30px;
}

.game-types__options {
    width            : 100%;
    min-height       : calc(100vh - #{$header-height} - #{$choose-height});
    padding          : 20px 0;
    display          : flex;
    align-items      : center;
    flex-direction   : column;
    background-color : $sunglow;
}
</style>

<!-- override -->
<style lang="scss">
@import '../../sass/variables/index';

.game-types__choose .title { font-size: 25px; }
.game-types__options .button .button__full { background-color: $golden-tainoi !important; }
</style>

<template>
    <div class="game-types">
        <div class="game-types__content">
            <div class="game-types__choose">
                <element-title text="izberi igro"></element-title>
                <label class="game-types__label">Izberi področje igre</label>
                <img class="game-types__arrow" id="arrow"/>
            </div>
            <div class="game-types__options">
                <element-button text="intervali" @click.native="reroute('gameModes', { type: 'intervals' })"></element-button>
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
            const arrow = this.$el.querySelector('#arrow')
            const context = this
            arrow.onload = () => { context.loading = false }
            arrow.src = '/images/arrows/down.svg'
        })
    },
    methods: {
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
