<template>
    <sexy-button :color="color">
        <div class="rhythmKeyboard__selectionButton">
            <svg width="50px" height="50px" version="1.1" 
                xmlns="http://www.w3.org/2000/svg" 
                xmlns:xlink="http://www.w3.org/1999/xlink" xml:space="preserve" 
                xmlns:serif="http://www.serif.com/" style="margin-top: 6px; transform: scale(0.9); fill-rule:evenodd;clip-rule:evenodd;stroke-linecap:square;stroke-linejoin:round;stroke-miterlimit:1.5;">
                <g>
                    <path d="M2.625,14.651l44.75,0" style="fill:none;stroke:#363636;stroke-width:1.02px;" />
                    <path d="M2.625,19.29l44.75,0" style="fill:none;stroke:#363636;stroke-width:1.02px;" />
                    <path d="M2.625,23.928l44.75,0" style="fill:none;stroke:#363636;stroke-width:1.02px;" />
                    <path d="M2.625,28.567l44.75,0" style="fill:none;stroke:#363636;stroke-width:1.02px;" />
                    <path d="M2.625,33.206l44.75,0" style="fill:none;stroke:#363636;stroke-width:1.02px;" />
                </g>
                <g>
                    <g>
                        <path d="M16.064,16.179l0,16.753" style="fill:#020202;stroke:#000;stroke-width:1.02px;" />
                        <path d="M11.393,31.86c1.77,-1.164 3.93,-1.14 4.819,0.053c0.89,1.193 0.175,3.107 -1.595,4.27c-1.771,1.164 -3.93,1.14 -4.82,-0.053c-0.889,-1.193 -0.174,-3.106 1.596,-4.27Z" />
                    </g>
                    <g>
                        <path d="M27.969,16.179l0,16.753" style="fill:#020202;stroke:#000;stroke-width:1.02px;" />
                        <path d="M23.298,31.86c1.77,-1.164 3.93,-1.14 4.819,0.053c0.89,1.193 0.175,3.107 -1.596,4.27c-1.77,1.164 -3.929,1.14 -4.819,-0.053c-0.889,-1.193 -0.174,-3.106 1.596,-4.27Z" />
                    </g>
                    <g>
                        <path d="M39.874,16.179l0,16.753" style="fill:#020202;stroke:#000;stroke-width:1.02px;" />
                        <path d="M35.203,31.86c1.77,-1.164 3.929,-1.14 4.819,0.053c0.889,1.193 0.174,3.107 -1.596,4.27c-1.77,1.164 -3.93,1.14 -4.819,-0.053c-0.89,-1.193 -0.175,-3.106 1.596,-4.27Z" />
                    </g>
                </g>
                <rect x="3.719" y="10.736" width="42.381" height="28.524" style="fill:#0006e9;fill-opacity:0.2;" />
                <rect x="44.121" y="10.736" width="1.979" height="28.524" style="fill:#00abd0;fill-opacity:0.827451;fill-rule:nonzero;" />
                <rect x="3.719" y="10.736" width="1.979" height="28.524" style="fill:#ff1616;fill-opacity:0.741176;fill-rule:nonzero;" />
            </svg>
        </div>
    </sexy-button>
</template>

<style lang="scss" scoped>

    @import '../../../../../../sass/variables/index';

    .rhythmKeyboard__selectionButton {
        @include breakpoint-small-phone-landscape { margin-bottom: -20px; }
    }

</style>

<script>
import SexyButton from "../../../../elements/SexyButton.vue";

export default {
  props: ["color"],
  components: { SexyButton }
};
</script>

