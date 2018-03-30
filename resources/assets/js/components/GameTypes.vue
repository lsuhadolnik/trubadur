<style lang="scss" scoped>
@import '../../sass/app';

.games {
    width            : 100%;
    height           : calc(100% - 50px);
    background-color : $aero-blue;
}

.games__command-wrapper {
    padding        : 20px 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.games__command {
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
    <div class="games">
        <div class="games__command-wrapper">
            <div class="games__command" @click="open('gameModes', { type: 'intervals' })">Intervali</div>
        </div>
    </div>
</template>

<script>
export default {
    methods: {
        open (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
