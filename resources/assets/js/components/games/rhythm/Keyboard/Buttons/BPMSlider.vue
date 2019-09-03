<template>
    
    <sexy-slider :color="metronomeBorder" :value="bpmObject" :valueKey="valueKey" :from="20" :to="250" :clickAction="toggleMetronome">
        <div class="rhythmKeyboard__BPMSlider__BPMIndicator" v-if="!metronomeToggled">
            <div class="rhythmKeyboard__BPMSlider__BPMValue">{{bpmObject[valueKey]}}</div>
            <div class="rhythmKeyboard__BPMSlider__BPMPrompt">BPM</div>
        </div>
        <div class="rhythmKeyboard__BPMSlider__BPMIndicator" v-else>
            <div class="rhythmKeyboard__BPMSlider__TinyText">Metro<br>nom<br>{{ bpmObject.metronome ? '✅' : '⛔' }} </div>
        </div>
    </sexy-slider>

</template>

<script>

import SexySlider from "../../../../elements/SexySlider.vue"

export default {
    
    props: ["bpmObject", "valueKey"],
    components: { SexySlider },

    data() {
        return {
            metronomeToggled: false,
            changeInterval: null,
            lastChanged: null
        }
    },

    computed: {

        buttonColor() {
            if(!this.metronomeToggled){
                return "cabaret";
            }else {
                if(this.bpmObject.metronome) return "green";
                return "red";
            }
        },

        metronomeBorder() {
            if(this.bpmObject.metronome)
            return "green";

            return "cabaret";
        }
    },

    methods: {
        toggleMetronome() {

            if(!this.lastChanged || (((new Date()).getTime()) - this.lastChanged) > 600 ){
                this.lastChanged = (new Date()).getTime();


                this.metronomeToggled = true;

                this.bpmObject.metronome = !this.bpmObject.metronome;
                let out = this;
                
                if(this.changeInterval){
                    clearTimeout(this.changeInterval);
                }
                this.changeInterval = setTimeout(() => {
                    out.metronomeToggled = false;
                }, 600);
            }

        }
    }

}
</script>


<style lang="scss" scoped>

    @import '../../../../../../sass/variables/index';

    .rhythmKeyboard__BPMSlider__BPMIndicator{
        font-size: 16px;
        font-family: $font-bold;
    }

    .rhythmKeyboard__BPMSlider__TinyText{
        font-size: 12px;
    }
    
</style>
