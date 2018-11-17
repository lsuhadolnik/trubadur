<template>
    <div class="two-rows-button__wrap">

        <div class="two-rows-button__hollow">
            
            <div class="two-rows-button__top-space">
                &lt;
            </div>

            <div class="two-rows-button__bottom-space">
                &gt;
            </div>

        </div>

        <div class="two-rows-button__full"></div>

    </div>
</template>

<script>
export default {

}
</script>

<style lang="scss" scoped>

    @import '../../../sass/variables/index';


    .two-rows-button__wrap {
    display    : inline-block;
    padding    : 0 10px 0 0;
    position   : relative;
    width      : 50px;
    height     : 100px;
    cursor     : pointer;
    transition : filter $button-transition-time linear;

    &:hover { filter: brightness(0.85); }
    }

.button--disabled {
    cursor         : not-allowed;
    pointer-events : none;
}

.two-rows-button__hollow {
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

.two-rows-button__full {
    position      : absolute;
    top           : 6px;
    left          : 6px;
    width         : 100%;
    height        : 100%;
    border-radius : 6px;
    background-color: $jaffa;
}

.button__green{
    background-color: $sea-green;
}

.button__orange{
    background-color: $jaffa;
}

</style>
