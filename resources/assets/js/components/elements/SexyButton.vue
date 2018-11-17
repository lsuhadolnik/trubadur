<template>
    <div class="button" :class="{ 'button--disabled': disable }">
        <div :class="['button__hollow', customClass]">
            {{text}}
            <slot></slot>
        </div>
        <div class="button__full" :class="{'button__green':color=='green', 'button__orange':color=='orange'}"></div>
    </div>
</template>

<style lang="scss" scoped>

@import '../../../sass/variables/index';

/*.button {
    display: inline-block;
    position: relative;
}

.button__hollow {
    position: relative;
    z-index: 100;
    display: inline-block;
    border-radius: 6px;
    border: 3px solid $black;
    text-align: center;
    min-width: 50px;
    min-height: 50px;
    vertical-align: middle;
}

.button__full {
    z-index: 50;
    position: absolute;
    top: 6px;
    left: 6px;
    display: inline-block;
    background: $sea-green;
    color: $sea-green;
    border-radius: 6px;
    min-width: 50px;
    min-height: 50px;
}*/

.button {
    display    : inline-block;
    //padding    : 0 10px 0 0;
    position   : relative;
    width      : 50px;
    height     : 50px;
    cursor     : pointer;
    transition : filter $button-transition-time linear;

    &:hover { filter: brightness(0.85); }
}

.button--disabled {
    cursor         : not-allowed;
    pointer-events : none;
}

.button__hollow {
    position        : absolute;
    width           : 100%;
    height          : 100%;
    border          : 3px solid $black;
    border-radius   : 6px;
    display         : flex;
    align-items     : center;
    justify-content : center;
    text-align      : center;
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

.button__green{
    background-color: $sea-green;
}

.button__orange{
    background-color: $jaffa;
}

</style>

<script>

import { stringProp, booleanProp } from '../../utils/propValidators'

export default {

    props: {
        text: stringProp(false),
        disable: booleanProp(false),
        customClass: stringProp(false),
        color: stringProp(false)
    }
}

</script>

