<template>
    <div class="intervals__progress-chapters">
        <!--<div class="intervals__progress-chapter" v-for="n in chapterCount">
            <div class="intervals__progress-question">&nbsp;</div>
        </div>-->
    </div>
</template>

<script>
export default {

    props: {
        chapterCount: {
            type: Number,
            default: 3
        },

    },

    computed: {
        chapterWidth(){
            return { 'width': (chapter > n ? 100 : ((number - 1) * 100 / nQuestions)) + '%' }
        }
    }

}
</script>

<style lang="scss" scoped>

.intervals__progress-chapter {
    background: red;
}

</style>
