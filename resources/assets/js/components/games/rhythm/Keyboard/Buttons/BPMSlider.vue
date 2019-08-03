<template>
    
    <sexy-slider color="cabaret" :value="bpmObject" :valueKey="valueKey" :from="20" :to="250">
        <div class="rhythmKeyboard__BPMSlider__BPMIndicator">
            <div class="rhythmKeyboard__BPMSlider__BPMValue">{{bpmObject[valueKey]}}</div>
            <div class="rhythmKeyboard__BPMSlider__BPMPrompt">BPM</div>
        </div>
    </sexy-slider>

</template>

<script>

import SexyButton from "../../../../elements/SexyButton.vue"
import SexySlider from "../../../../elements/SexySlider.vue"

export default {
    
    props: ["bpmObject", "valueKey"],
    components: { SexyButton, SexySlider }

}
</script>


<style lang="scss" scoped>

    @import '../../../../../../sass/variables/index';

    .rhythmKeyboard__BPMSlider__BPMIndicator{
        font-size: 16px;
        font-family: $font-bold;
    }
    
</style>
