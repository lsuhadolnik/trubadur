<template>
    
    <div>

        Nastavitve
        
        Koliko točk odštejem težavnosti takta in vaje, ko uporabnik uspešno opravi takt in vajo
        <input type="number" v-model="difficultyChange.decrease_on_success" />

        Koliko točk prištejem težavnosti takta in vaje, ko uporabnik neuspešno odda
        <input type="number" v-model="difficultyChange.increase_on_failure" />

    </div>

</template>

<script>
export default {
    
    data() {
        return {
            difficultyChange: {
                decrease_on_success: 1,
                increase_on_failure: 1
            }
        }
    }

}
</script>

<style lang="sass" scoped>

</style>


