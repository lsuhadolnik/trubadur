<template>
    
    <sexy-button color="sunglow" :percents="percents" customClass="rhythmKeyboard__playButton">
        <icon name="stop" v-if="playing"/>
        <div v-else class="small-font-button rhythmKeyboard__playButton__buttonText">{{text}}</div>
    </sexy-button>

</template>

<script>

import SexyButton from "../../../../elements/SexyButton.vue"

export default {
    
    props: ['percents', 'text', 'playing'],
    components: { SexyButton }

}
</script>


<style lang="scss" scoped>


    @import '../../../../../../sass/variables/index';

    .rhythmKeyboard__playButton__buttonText {
        font-size: 8pt;
        font-family: $font-bold;

        @include comfortable-screen {
            font-size: 10pt;
        }
    }
    
</style>
