<template>
    <div class="circle-timer__wrap">
        <svg id="timer"></svg>
    </div>
</template>

<style lang="scss" scoped>

    .circle-timer__wrap {
        display: inline-block;
    }

</style>


<script>
export default {
    
    props: {
        time: {
            type: Number,
            default: 120
        }
    },

    data() {
        return {
            timer: null,
        }
    },

    mounted() {
        this.timer = new TimerProgress({ // eslint-disable-line no-undef
                'container': this.$el.querySelector('#timer'),
                'width-container': 80,
                'height-container': 80,
                'stroke-width': 10,
                'color-circle': '#F8A16E',
                'color-path': '#EB7D3D',
                'color-alert': '#D2495F',
                'font-size': 20,
                'font-family': 'GothamRounded-Bold'
        })
    },

    methods: {

        run(){
            this.timer.run(this.time, 50)
        }

    }

}
</script>
