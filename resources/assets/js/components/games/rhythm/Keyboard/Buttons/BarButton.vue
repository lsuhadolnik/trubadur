<template>
    
    <sexy-button color="green">
        <div class="rhythm-game__bar-button__text musisync">
            <div class="rhythm-game__bar-button__lines">&#x00A9;&#x00A9;</div>
            <div class="rhythm-game__bar-button__barline">&#x2019;</div>
        </div>
    </sexy-button>

</template>

<script>

import SexyButton from "../../../../elements/SexyButton.vue"

export default {
    
    props: [],
    components: { SexyButton }

}
</script>


<style lang="scss" scoped>

    .rhythm-game__bar-button__text {
        position: relative;
        width: 50px;
        height: 50px;
    }

    .rhythm-game__bar-button__lines {
        position: absolute;
        top: 2px;
        left: 6px;
    }

    .rhythm-game__bar-button__barline {
        position: absolute;
        top: -4px;
        left: 50%;
        font-size: 39px;
    }

    .norfolk {
        font-family: Norfolk !important;
    }

    .musisync {
        font-family: MusiSync !important;
    }
    

</style>
