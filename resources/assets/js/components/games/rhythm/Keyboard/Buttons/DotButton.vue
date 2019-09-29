<template>
    
    <sexy-button color="green">
        <div class="rhythmKeyboard__dotButton">
            &#x002E;
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

    .rhythmKeyboard__dotButton {
        font-size: 53px; 
        line-height: 0; 
        padding: 0 0 10px 10px;
        font-family: MusiSync !important;
        margin-bottom: 33px;
        margin-left: -5px;
    }
    
</style>
