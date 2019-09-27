<template>
    <sexy-button :color="buttonColor">
        <div class="rhythmKeyboard__BPMText__BPMIndicator">
            <div class="rhythmKeyboard__BPMText__BPMValue">{{text}}</div>
            <div class="rhythmKeyboard__BPMText__BPMPrompt">BPM</div>
        </div>    
    </sexy-button>
</template>

<script>

import SexyButton from "../../../../elements/SexyButton.vue"

export default {
    
    props: ["text", "selected"],
    components: { SexyButton },
    computed: {
        buttonColor() {

            if(this.selected){
                return "sunglow";
            }
            return "green";

        }
    }

}
</script>


<style lang="scss" scoped>

    @import '../../../../../../sass/variables/index';

    .rhythmKeyboard__BPMText__BPMIndicator{
        font-size: 16px;
        font-family: $font-bold;
    }

    .rhythmKeyboard__BPMText__TinyText{
        font-size: 12px;
    }
    
</style>

