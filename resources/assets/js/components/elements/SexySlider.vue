<template>
    <div class="button" :class="theClass" ref="theSlider">
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

import { stringProp, booleanProp, numberProp } from '../../utils/propValidators'

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
        value: {
            type: Object,
            required: true,
            default: null
        },
        valueKey: stringProp(true),
        from: numberProp(true),
        to: numberProp(true),
        percents: {
            type: Number,
            required: false,
            default: 0
        },
        cols: {
            type: Number,
            required: false,
            default: 1
        },
    },

    data: function() {
        return {
            lastTouch: 0,
            inTouch: false
        };
    },

    mounted() {

        let out = this;

        this.$refs.theSlider.addEventListener("touchstart", function(e){
            
            out.lastTouch = e.touches[0].screenX;
            out.inTouch = true;
            
        }, false);

        this.$refs.theSlider.addEventListener("touchend", function(e){
            
            out.inTouch = false;
            
        }, false);

        this.$refs.theSlider.addEventListener("touchmove", function(e){
            
            let newValue = out.value[out.valueKey] + parseInt((e.touches[0].screenX - out.lastTouch) / 2);
            newValue = Math.min(newValue, out.to);
            newValue = Math.max(newValue, out.from);
            out.value[out.valueKey] = newValue;

            out.lastTouch = e.touches[0].screenX;

        }, false);

    }

}

</script>

