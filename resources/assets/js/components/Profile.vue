<style lang="scss" scoped>
@import '../../sass/variables/index';

.profile { width: 100%; }

.profile__content {
    width          : 100%;
    padding        : 20px 0 $bottom-padding 0;
    display        : flex;
    align-items    : center;
    flex-direction : column;
}

.profile__tabs {
    width           : 100%;
    padding         : 0 5%;
    display         : flex;
    justify-content : center;
}

.profile__tab {
    width           : calc(100% / 3);
    height          : 50px;
    border-radius   : 10px 10px 0 0;
    display         : flex;
    align-items     : center;
    justify-content : center;
    text-align      : center;
    cursor          : pointer;

    &:nth-child(2) { margin: 0 5%; }
}

.profile__tab--me     { background-color : $jaffa;   }
.profile__tab--levels { background-color : $neptune; }
.profile__tab--badges { background-color : $fern;    }
</style>

<template>
    <div class="profile">
        <div class="profile__content">
            <div class="profile__tabs">
                <div class="profile__tab profile__tab--me" @click="openTab('me')">JAZ</div>
                <div class="profile__tab profile__tab--levels" @click="openTab('levels')">NIVOJI</div>
                <div class="profile__tab profile__tab--badges" @click="openTab('badges')">DOSEŽKI</div>
            </div>
            <profile-me :id="id" @open-tab="openTab" v-if="openedTab === 'me'"></profile-me>
            <profile-levels :id="id" v-if="openedTab === 'levels'"></profile-levels>
            <profile-badges :id="id" v-if="openedTab === 'badges'"></profile-badges>
        </div>
    </div>
</template>

<script>
export default {
    props: ['id'],
    data () {
        return {
            openedTab: 'me'
        }
    },
    methods: {
        openTab (tab) {
            this.openedTab = tab
        }
    }
}
</script>
