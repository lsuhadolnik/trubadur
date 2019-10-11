<template>
    
    <sexy-button :color="color">
        <div class="norfolk-note-padding thirtytwo-note">
            <div class="td-base"> &#x0071;</div>
            <div class="td-flag1">&#x006A;</div>
            <div class="td-flag2">&#x006A;</div>
            <div class="td-flag3">&#x006A;</div>
        </div>
    </sexy-button>

</template>

<script>

import SexyButton from "../../../../elements/SexyButton.vue"

export default {
    
    props: ['color'],
    components: { SexyButton }

}
</script>


<style lang="scss" scoped>

    .thirtytwo-note {
        margin-top: 23px;
    }

    .thirtytwo-note {

        .td-flag1, .td-flag2, .td-flag3{
            font-size: 30px;
            margin-left: 17px;
        }

        .td-base{
            margin-top: -18px;
        }

        .td-flag1{
            margin-top: -89px;
        }

        .td-flag2{
            margin-top: -53px;
        }
        
        .td-flag3{
            margin-top: -53px;
        }

    }

</style>


