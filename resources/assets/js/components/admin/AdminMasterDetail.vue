<template>
    <div class="MasterDetail">
        <div class="masterDetail__masterView">
            <template v-slot:masterElement v-for="item in list"/>
        </div>
        <div>
            <template v-slot:detailElement />
        </div>
    </div>
</template>

<script>
export default {
    
    props: ['list']

}
</script>

<style lang="scss" scoped>

</style>


