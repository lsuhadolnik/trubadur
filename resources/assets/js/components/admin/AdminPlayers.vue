<template>
    <div class="main_adminPlayers">

        Generiraj vajo za nivo <input type="number" placeholder="nivo" v-model="level" />
        <button @click="generate">Generiraj</button>

        <div class="barze" >
            <RhythmBarInfo v-for="item in bars" ref="renderedBar" v-bind:key="item.id" :info="item" />
        </div>

    </div>
</template>

<script>

import RhythmBarInfo from './AdminRhythmBarInfo'
import { mapState, mapActions } from 'vuex'

export default {
    
    components: {RhythmBarInfo},

    data() {
        return {
            bars: [],
            level: 11
        }
    },

    methods: {
        ...mapActions(['generate10Exercises']),

        generate() {
            let out = this;
            this.generate10Exercises(this.level).then((res) => {
                out.bars = res;
            }).catch((e) => {
                console.error(e);
                alert("Napaka! \n\n"+e.message)
            });
        }
    }

}
</script>

<style lang="scss" scoped>

    .barze :first-child {
        margin-top: 40px;
    }

    .barze {
        background-color: #FFD15E;
    }

</style>


