<template>
    
    <sexy-button :color="checkButtonColor" :percents="percents">
        <div v-if="status == 'no'" class="rhythmKeyboard__checkButton__text">
            <div class="checkButton__firstLine">Preveri</div>
            <div class="checkButton__secondLine" v-if="textSecond"><span class="show-comfortable">Še &nbsp;</span>{{textSecond}}</div>
            <div class="checkButton__thirdLine" v-if="textThird">{{textThird}}</div>
        </div>
        <icon name="times" v-if="status == 'wrong'"/>
        <icon name="check" v-if="status == 'correct'"/>
        <icon name="refresh" v-if="status == 'waiting'" spin />
        <icon name="exclamation-circle" v-if="status == 'error'" />
        <div v-if="status == 'next'" class="rhythmKeyboard__checkButton__text">Naprej</div>
    </sexy-button>

</template>

<script>

import SexyButton from "../../../../elements/SexyButton.vue"

export default {
    
    props: ["status", "textSecond","textThird", "percents"],
    components: { SexyButton },
    computed: {

        checkButtonColor(){

            let colors = {
                no: "cabaret",
                wrong: "red",
                correct: "green",
                next: "sunglow"
            }

            if(this.status in colors) return colors[this.status];
            return "cabaret";
        },

    }

}
</script>


<style lang="scss" scoped>

    @import '../../../../../../sass/variables/index';

    .rhythmKeyboard__checkButton__text{
        font-size: 8pt;
        font-family: $font-bold;
    }

    .show-comfortable{

        display: none;

        @include comfortable-screen {
            display:inline-block;
        }

    }
    
    
</style>
