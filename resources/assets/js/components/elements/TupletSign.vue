<template>
    
    <div class="tuplet-sign tuplet-sign-wrap">
        <svg width="50" height="50" xmlns="http://www.w3.org/2000/svg">
            <g>
                <path id="svg_1" d="m7.9782,37.16994l-0.00009,-13.83671l7.43849,0" stroke-width="1.5" fill="none" stroke="#000"/>
                <path id="svg_2" d="m6.45841,22.6823" fill-opacity="null" stroke-opacity="null" stroke-width="1.5" stroke="#000" fill="none"/>
                <path id="svg_4" d="m32.66667,23.28149c0.16617,0 9.47177,0 9.47176,-0.17039c0.00001,0.17039 0.00001,14.89317 0,14.72164" stroke-width="1.5" fill="none" stroke="#000"/>
                <text xml:space="preserve" text-anchor="start" font-family="Helvetica, Arial, sans-serif" font-weight="bold" :font-size="textSize" id="svg_5" :y="textY" :x="textX" stroke-width="0" stroke="#000" fill="#000000">{{num}}</text>
            </g>
        </svg>
    
    </div>

</template>

<style>

    .tuplet-sign-wrap{
        margin-top: 18px;
    }

</style>


<script>

export default {

    props: [ "num" ],

    computed: {

        // x: 16, y: 27, font-size: 12px
        textX() {
            if(this.num && this.num.length > 1) return 16;
            else return 19;
        },

        textY() {
            if(this.num && this.num.length > 1) return 27;
            else return 30;
        },

        textSize() {
            if(this.num && this.num.length > 1) return 12;
            else return 20;
        },

        



    }

}

</script>
