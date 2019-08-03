<template>
    
    <div class="admin__rhythmBarInfo__barInfoEntry" >

        <!--<StaffView :notes="info.notes" />-->
        <div class="admin__rhythmBarInfo__barInfoEntry__staffView">
            {{info.content}}
        </div>
        <div class="admin__rhythmBarInfo__barInfoEntry__bottomInfo">

            <div class="admin__rhythmBarInfo__barInfoEntry__difficulty" >
                Takt. način: {{info.barInfo}}
            </div>

            <div class="admin__rhythmBarInfo__barInfoEntry__difficulty" >
                Težavnost: {{info.difficulty}}
            </div>

        </div>

    </div>

</template>

<script>
export default {
    props: ['info']
}
</script>