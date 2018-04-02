<style lang="scss" scoped>
@import '../../../sass/variables/index';

.title {
    padding          : 10px 20px;
    border-radius    : 6px;
    background-color : $black;
    color            : $golden-tainoi;
}
</style>

<template>
    <div class="title">{{ text | uppercase }}</div>
</template>

<script>
import { stringProp } from '../../utils/propValidators'

export default {
    props: {
        text: stringProp()
    }
}
</script>
