<template>
    <div class="button" :class="theClass">
        <div :class="['button__hollow', customClass]">
            {{text}}
            <slot></slot>
        </div>
        <div class="button__hollow button__percentIndicator" v-bind:style="{ width: percents + '%' }">&nbsp;</div>
        <div class="button__full" :class="{
            'button__green':color=='green', 
            'button__orange':color=='orange', 
            'button__red':color=='red',
            'button__cabaret':color=='cabaret',
            'button__sunglow':color=='sunglow'}"></div>
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
    touch-action: manipulation;
    -webkit-touch-action: manipulation;

    &:hover { filter: brightness(0.85); }
}

.button--disabled {
    cursor         : not-allowed;
    pointer-events : none;
}

.button__hidden{
    visibility: hidden;
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
    top           : 3px;
    left          : 3px;
    width         : 95%;
    height        : 96%;
    border-radius : 6px;
}

.button__percentIndicator{
    background: rgba(0,0,0,0.2);
    width: 0;
    border: transparent;
}

.button__green{
    background-color: $sea-green;
}

.button__orange{
    background-color: $jaffa;
}

.button__red{
    background-color: $neon-red;
}

.button__cabaret{
    background-color: $cabaret;
}

.button__sunglow{
    background-color: $sunglow;
}

.button_1_col{
    width: 50px;
}

.button_2_col{
    width: 110px;
}

.button_3_col{
    width: 170px;
}

</style>

<script>

import { stringProp, booleanProp } from '../../utils/propValidators'

export default {

    methods: {

    },

    computed:{

        theClass: function(){
            var k = "";

            if(this.disable){
                k += 'button--disabled ';
            }

            if(this.hidden){
                k += 'button__hidden';
            }

            if(this.customClass != null){
                k+= ' '+ this.customClass;
            }

            if(this.cols){
                switch(this.cols){
                    case 1:
                        k += " button_1_col";
                    break;

                    case 2:
                        k += " button_2_col";
                    break;

                    case 3:
                        k += " button_3_col";
                    break;

                }
            }

            return k;
        }

    },

    props: {
        text: stringProp(false),
        disable: booleanProp(false),
        customClass: stringProp(false),
        color: stringProp(false),
        hidden: booleanProp(false),
        percents: {
            type: Number,
            required: false,
            default: 0
        },
        cols: {
            type: Number,
            required: false,
            default: 1
        }
    }
}

</script>

