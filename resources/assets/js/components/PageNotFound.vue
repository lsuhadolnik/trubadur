<style lang="scss" scoped>
@import '../../sass/variables/index';

.page-not-found { width: 100%; }

.page-not-found__content {
    width           : 100%;
    height          : calc(100vh - #{$header-height});
    display         : flex;
    justify-content : center;
    align-items     : center;
    flex-direction  : column;
}

.page-not-found__notification { font-size: 20px; }

.page-not-found__redirect {
    margin      : 0;
    font-size   : 17px;
    font-family : $font-regular;
}

.page-not-found__redirect--dynamic { width: 180px; }

.page-not-found__link {
    text-decoration : underline;
    cursor          : pointer;
}
</style>

<template>
    <div class="page-not-found">
        <div class="page-not-found__content">
            <h2 class="page-not-found__notification">
                Iskana stran ne obstaja.
            </h2>
            <h3 class="page-not-found__redirect">
                Preusmeritev na
            </h3>
            <h3 class="page-not-found__redirect page-not-found__redirect--dynamic">
                <span class="page-not-found__link" @click="reroute('dashboard')">domačo stran</span> v {{ time / 1000 }} s...
            </h3>
        </div>
    </div>
</template>

<script>
export default {
    data () {
        return {
            interval: 1000,
            time: 5000
        }
    },
    created () {
        const intervalId = setInterval(() => {
            this.time -= this.interval
            if (this.time === 0) {
                clearInterval(intervalId)
                this.reroute('dashboard')
            }
        }, this.interval)
    },
    methods: {
        reroute (name, params = {}) {
            this.$router.push({ name: name, params: params })
        }
    }
}
</script>
