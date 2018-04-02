<style lang="scss" scoped>
@import '../../../sass/variables/index';

.button {
    position   : relative;
    width      : 175px;
    height     : 50px;
    cursor     : pointer;
    transition : filter $button-transition-time linear;

    &:hover { filter: brightness(0.85); }
}

.button__hollow {
    position        : absolute;
    width           : 100%;
    height          : 100%;
    border          : 3px solid black;
    border-radius   : 6px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    z-index         : 1;
}

.button__full {
    position      : absolute;
    top           : 6px;
    left          : 6px;
    width         : 100%;
    height        : 100%;
    border-radius : 6px;
}
</style>

<template>
    <div class="button">
        <div class="button__hollow">{{ text | uppercase }}</div>
        <div class="button__full"></div>
    </div>
</template>

<script>
import { stringProp } from '../../utils/propValidators'

export default {
    props: {
        text: stringProp()
    }
}
</script>
