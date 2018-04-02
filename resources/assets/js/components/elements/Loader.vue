<style lang="scss" scoped>
@import '../../../sass/variables/index';

.loader {
    position  : absolute;
    top       : 50%;
    left      : 50%;
    transform : translate(-50%, -50%);
    width     : 100px;
    height    : 100px;

    @include breakpoint-phone {
        width  : 75px;
        height : 75px;
    }
}
</style>

<template>
    <img class="loader" src="/images/loader.svg"/>
</template>

<script>
export default { }
</script>
