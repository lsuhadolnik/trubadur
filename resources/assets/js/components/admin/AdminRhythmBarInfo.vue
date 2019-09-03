<template>
    
    <div class="admin__rhythmBarInfo__barInfoEntry" >

        <!--<StaffView :notes="info.notes" />-->
        <div class="admin__rhythmBarInfo__barInfoEntry__staffView">
            <StaffView ref="staff_view" :bar="info.timeSignature" :hideTimeSignatures="true" :enabledContexts="['zoomview']" >
                <div class="rhythm-game__staff__second-row">
                    <div class="admin__rhythmBarInfo__barInfoEntry__staffView__staff" :id="'rhythmBar'+this._uid"></div>
                </div>
            </StaffView>
        </div>
        <div class="admin__rhythmBarInfo__barInfoEntry__bottomInfo">

            <div class="admin__rhythmBarInfo__barInfoEntry__text admin__rhythmBarInfo__barInfoEntry__barInfo" v-if="info.timeSignature">
                #{{info.id}} <!--Takt. način: {{takt(info.timeSignature)}}-->
            </div>

            <!--<div class="admin__rhythmBarInfo__barInfoEntry__text admin__rhythmBarInfo__barInfoEntry__difficulty" >
                Težavnost: {{info.difficulty}}
            </div>-->

        </div>

    </div>

</template>

<style lang="scss" scoped>
    
    @import '../../../sass/variables/index';

    .rhythm-game__staff__second-row {
        height: auto !important;
        overflow: hidden !important;
    }

    .admin__rhythmBarInfo__barInfoEntry__text {
        display: inline-block;
        padding: 0 0 14px 9px;
    }

    .rhythm-game__staff__second-row[data-v-5a6b0eb1] {
        transform: scale(1.5) translate(17%);
        height: 102px;
    }

    .admin__rhythmBarInfo__barInfoEntry {
        border-bottom: 1px rgba(0,0,0,0.3) solid;
        font-family: $font-regular;
    }

    .admin__rhythmBarInfo__barInfoEntry:hover {
        background: lighten($color: $jaffa, $amount: 10);
        cursor: pointer;
    }

    .admin__rhythmBarInfo__barInfoEntry__staffView__staff {
        transform: scale(0.6) translateX(-33%);
    }

</style>


<script>

import StaffView from '../games/rhythm/StaffView.vue'

export default {
    
    props: ['info'],
    components: {
        StaffView
    },

    methods: {

        takt(a) {
            if(a.subdivisions){
                return a.subdivisions.map((k) => {return k.n +"/"+ k.d}).join("+");
            }

            return a.num_beats +"/"+ a.base_note;
        },

        render(){
            if(this.info.notes){
                this.$refs.staff_view.render(this.info.notes);
            }else {
                this.$refs.staff_view.render(this.info.content);
            }

            
        }

    },

    mounted() {
        
        this.$refs.staff_view.CTX.zoomview.id = 'rhythmBar' + this._uid;
        this.$refs.staff_view.CTX.zoomview.init = function(vue) {
            let sR = this.parentElement;
        };
        
        this.$refs.staff_view.init("RhythmView");
        this.render();
    }

}
</script>