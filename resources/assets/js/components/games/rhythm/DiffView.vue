<template>

    <div class="rhythm-game__diff">
        
        <h1 style="text-align: center;font-size: 40px;">{{playfulTitle}}</h1>

        <h1 class="diff-prompt">Taka je bila vaja:</h1>
        <StaffView ref="staff_view1" :bar="bar" :enabledContexts="['zoomview']" >
            <div class="rhythm-game__diff__zoomview1">
                <div id="diff-zoom-view1"></div>
            </div>
        </StaffView>
        
        <h1 class="diff-prompt">Tole je bilo vpisano:</h1>
        <StaffView ref="staff_view2" :bar="bar" :enabledContexts="['zoomview']">
            <div class="rhythm-game__diff__zoomview2">
                <div id="diff-zoom-view2"></div>
            </div>
        </StaffView>
        
        <div class="button-holder">
            <SexyButton color="green" @click.native="dismiss()" :cols="2" >Vredu</SexyButton>
        </div>

    </div>

</template>

<style lang="scss" scoped>

    @import '../../../../sass/variables/index';


    .rhythm-game__diff__zoomview1, .rhythm-game__diff__zoomview2  {
        overflow-x: scroll;
        -webkit-overflow-scrolling: touch;
        overflow-scrolling: touch;
        height: 163px;
        overflow-y: hidden;

        @include breakpoint-phone-landscape { height: 150px; }
    }

    #diff-zoom-view1, #diff-zoom-view2 {
        -webkit-transform: scale(2) translate(25%, 25%);
        transform: scale(2) translate(25%, 25%);
    }

    .diff-prompt{
        padding: 0px;
        margin: 10px 0 0 0;
        font-size: 26px;
        text-align: center;
    }

    .button-holder{
        display: flex;
        width: 100%;
        justify-content: center;
    }


</style>


<script>

import StaffView from "./StaffView.vue"

import SexyButton from "../../elements/SexyButton.vue"


export default {

    props: [
        'dismiss', "bar", "success"
    ],

    components: {SexyButton, StaffView},

    data () {
        return {

        }
    },

    computed: {

        playfulTitle() {
            
            let successTexts = ["Bravo! ✅💪", "Useplo ti je! 🎆🎆", "Čestitam! 👏", "Odlično! 💪", "Zakon! Obvladaš! 🎉"];
            let failureTexts = ["Oooh... 😢", "Ups... 😕", "O ne!", "No ja... 🙂", "Več sreče prihodnjič. 😉", "Skoraj ti je uspelo...", "Še čisto malo!"];
            
            let pickRandom = function(a){
                let idx = parseInt(Math.random() * (a.length - 1));
                return a[idx];
            }

            if(this.success){
                return pickRandom(successTexts);
            } else {
                return pickRandom(failureTexts);
            }
        }

    },

    methods: {

        render(exerciseNotes, userNotes) {

            this.$refs.staff_view1.render(exerciseNotes);
            this.$refs.staff_view2.render(userNotes);    
        },

    },
    mounted(){

        let out = this;
        let z1 = document.getElementById("diff-zoom-view1");
        let z2 = document.getElementById("diff-zoom-view2");

        this.$refs.staff_view1.CTX.zoomview.id = "diff-zoom-view1";
        this.$refs.staff_view1.CTX.zoomview.init = function() {
            // this = descriptor object
            
            var sR = this.parentElement;
            this.parentElement.onscroll = function(e){
                z2.parentElement.scroll(sR.scrollLeft, 0);
                return false;
            }
        };


        this.$refs.staff_view2.CTX.zoomview.id = "diff-zoom-view2";
        this.$refs.staff_view2.CTX.zoomview.init = function(vue) {
            // this = descriptor object
            
            var sR = this.parentElement;
            sR.onscroll = function(e){
                z1.parentElement.scroll(sR.scrollLeft, 0);
                return false;
            }

        };


        this.$refs.staff_view1.init("diffView1");
        this.$refs.staff_view2.init("diffView2");

    },

}
</script>
