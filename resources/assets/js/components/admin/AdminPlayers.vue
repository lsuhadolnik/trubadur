<template>
    <div class="main_adminPlayers">

        <loader v-show="displayState == 'loading'"></loader>

        Generiraj vajo za nivo <input type="number" placeholder="nivo" v-model="level" />
        <button @click="generate">Generiraj</button>

        <div class="barze" >
            <RhythmBarInfo v-for="item in bars" ref="renderedBar" v-bind:key="item.id" :hideTimeSignature="false" :info="item" @click.native="openRhythmView(item.id)"/>
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
            level: 11,

            displayState: 'ready'
        }
    },

    methods: {
        ...mapActions(['generate10Exercises']),

        generate() {
            let out = this;
            this.displayState='loading';
            this.generate10Exercises(this.level).then((res) => {
                out.bars = res;
                this.displayState='ready';
            }).catch((e) => {
                console.error(e);
                alert("Napaka! \n\n"+e.message)
                this.displayState='ready';
            });
        },

        openRhythmView(exerciseId) {
            let routeData = this.$router.resolve({name: 'rhythm', params: {exerciseId}});
            console.log(routeData.href);
            debugger;
            window.open(routeData.href, '_blank');
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


