<template>
    <div class="admin__rhythmBars">

        <div class="admin_rhythmBars_masterView" >

            <RhythmBarInfo v-for="item in bars" v-bind:key="item.id" :info="item" />

        </div>

        <div class="admin_rhythmBars_detailView" >

        </div>

    </div>
</template>

<script>

import { mapState, mapActions } from 'vuex'
import RhythmBarInfo from './AdminRhythmBarInfo.vue'

export default {
    
    data() {
        return {
            currentPage: 1,
            allPages: null,
            bars: []
        }
    },

    components: {
        RhythmBarInfo
    },

    
    methods: {
        ...mapActions(['fetchRhythmBars']),

        loadBarsPage() {

            let out = this;

            return out.fetchRhythmBars({pageNum: out.currentPage}).then((res) => {

                if(res.current_page){
                    out.currentPage = res.current_page;
                }

                if(res.data && res.data.length >= 0){
                    out.processBars(res.data);
                }

            });
        },

        processBars(barsData) {

            // parse JSON bars
            this.bars = barsData;
            for(let i = 0; i < this.bars.length; i++){
                this.bars[i].content = JSON.parse(this.bars[i].content);
            }
        }
    },

    mounted() {

        this.loadBarsPage();

    }

}
</script>

<style lang="scss" scoped>

</style>


