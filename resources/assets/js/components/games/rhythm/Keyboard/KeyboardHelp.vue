<template>

    <div class="helpView__wrap">

        <div class="helpView">

            <h1>Pomoč</h1>
            <div class="row">
                <div class="col-md-6">


                    <!-- BASICS -->
                    <div class="helpView__view view-1" v-if="view == 1">

                        <div class="helpView__text-line">S pritiskom na enega od teh gumbov dodaš noto na črtovje:</div>
                        <div class="helpView__buttons norfolk-row">
                            <sexy-button color="green"><div class="norfolk-note-padding">&#x0068;</div></sexy-button>
                            <sexy-button color="green"><div class="norfolk-note-padding">&#x0071;</div></sexy-button>
                            <sexy-button color="green"><div class="norfolk-note-padding">&#x0065;</div></sexy-button>
                            <sexy-button color="green"><div class="norfolk-note-padding">&#x0078;</div></sexy-button>
                            <thitry-two-button color="green" />
                        </div>

                        <div class="helpView__text-line">s temi pa dodaš pavze:</div>
                        <div class="helpView__buttons norfolk-row">
                            <sexy-button color="red">&#x0152;</sexy-button>
                            <sexy-button color="red">&#x0152;</sexy-button>
                            <sexy-button color="red">&#x2030;</sexy-button>
                            <sexy-button color="red">&#x2248;</sexy-button>
                            <sexy-button color="red">&#x00AE;</sexy-button>
                        </div>

                        <div class="helpView__text-line">Ta gumb doda taktnico:</div>
                        <div class="helpView__buttons musisync-row">
                            <bar-button />
                        </div>

                        <div class="helpView__text-line">Ta pa piko k trenutni noti, ki je pod kazalnikom:</div>
                        <div class="helpView__buttons musisync-row">
                            <dot-button />
                        </div>

                        <div class="helpView__text-line">Če se zmotiš, lahko noto pobrišeš s tem gumbom:</div>
                        <div class="helpView__buttons musisync-row">
                            <sexy-button color="cabaret">
                                <icon name="trash" scale="2" />
                            </sexy-button>
                        </div>
                        <div class="helpView__text-line">Gumb briše tudi vezaje in poddelitve.</div>

                    </div>

                    <!-- TIE, DELETE -->
                    <div class="helpView__view view-2" v-if="view == 2">

                        <div class="helpView__text-line">S tem gumbom ustvariš vezaj</div>
                        <sexy-button customClass="musisync" color="green">
                            <span class="tie-text">u</span>
                        </sexy-button>
                        <div class="helpView__text-line">Kako deluje? Vezaj lahko narediš na dva načina:</div>
                        <div class="helpView__text-line">Lahko se postaviš med dve noti in pritisneš ta gumb. </div>
                        <div class="helpView__text-line">
                            <div class="helpView__cursor musisync" style="display: inline-block; width: 99px;">
                                <div class="helpView__cursor__lines">&#x00A9;&#x00A9;</div>
                                <div class="helpView__cursor__notes " style="">q</div>
                                <div class="helpView__cursor__cursor" style="left: 9px;">&#x2019;</div>
                                <div class="helpView__cursor__notes " style="left: 48px;">q</div>
                            </div>
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            <sexy-button customClass="musisync" color="green">
                                <span class="tie-text">u</span>
                            </sexy-button>
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            <div class="helpView__cursor musisync" style="display: inline-block; width: 99px;">
                                <div class="helpView__cursor__lines">&#x00A9;&#x00A9;</div>
                                <div class="helpView__cursor__notes " style="">q</div>
                                <div class="helpView__cursor__cursor" style="left: 9px;">&#x2019;</div>
                                <div class="helpView__cursor__notes " style="left: 48px;">q</div>
                                <div class="helpView__cursor__notes helpView__tie" style="left: 16px; font-size: 52px; top: 60px !important;">U</div>
                            </div>
                        </div>

                        <div class="helpView__text-line">Drugi način pa je vezaj "na slepo" </div>
                        <div class="helpView__text-line">Ko si čisto na zadnji noti, pritisni vezaj. </div>
                        <div class="helpView__text-line">Pokazala se bo nova, modra nota.</div>
                        <div class="helpView__text-line">Potem vpiši naslednjo noto, ki jo želiš povezati in ta bo nadomestila modro noto.</div>
                        <div class="helpView__text-line">
                            <div class="helpView__cursor musisync" style="display: inline-block; width: 162px;">
                                <div class="helpView__cursor__lines">&#x00A9;&#x00A9;&#x00A9;&#x00A9;</div>
                                <div class="helpView__cursor__notes " style="">qq</div>
                                <div class="helpView__cursor__cursor" style="left: 56px;">&#x2019;</div>
                                <div class="helpView__cursor__notes " style="left: 48px;">q</div>
                            </div>
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            
                            
                            
                            <sexy-button customClass="musisync" color="green">
                                <span class="tie-text">u</span>
                            </sexy-button>
                            
                            
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            <div class="helpView__cursor musisync" style="display: inline-block; width: 162px;">
                                <div class="helpView__cursor__lines">&#x00A9;&#x00A9;&#x00A9;&#x00A9;</div>
                                <div class="helpView__cursor__notes " style="">qq</div>
                                <div class="helpView__cursor__cursor" style="left: 106px;">&#x2019;</div>
                                <div class="helpView__cursor__notes " style="left: 97px; color: blue;">q</div>
                                <div class="helpView__cursor__notes helpView__tie" style="left: 61px; font-size: 52px; top: 60px !important;">U</div>
                            </div>
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            
                            
                            
                            <sexy-button color="green"><div class="norfolk-note-padding norfolk" style="font-size: 32px;">&#x0068;</div></sexy-button>
                            
                            
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            <div class="helpView__cursor musisync" style="display: inline-block; width: 162px;">
                                <div class="helpView__cursor__lines">&#x00A9;&#x00A9;&#x00A9;&#x00A9;</div>
                                <div class="helpView__cursor__notes " style="">qq</div>
                                <div class="helpView__cursor__cursor" style="left: 106px;">&#x2019;</div>
                                <div class="helpView__cursor__notes " style="left: 97px;">h</div>
                                <div class="helpView__cursor__notes helpView__tie" style="left: 61px; font-size: 52px; top: 60px !important;">U</div>
                            </div>
                        </div>

                    </div>

                    <!-- CURSOR -->
                    <div class="helpView__view view-3" v-if="view == 3">

                        <h2>Kazalnik</h2>
                        <div class="helpView__text-line">Zelena pokončna črta na notnem črtovju je kazalnik.</div>
                        <div class="helpView__cursor musisync">
                            <div class="helpView__cursor__lines">&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;</div>
                            <div class="helpView__cursor__notes helpView__cursor__notesBefore">qq</div>
                            <div class="helpView__cursor__cursor">&#x2019;</div>
                            <div class="helpView__cursor__notes helpView__cursor__notesAfter">qq</div>
                        </div>
                        <div class="helpView__text-line specialA1">Če pritisneš gumb za dodajanje note ali pavze, se bo nova nota ustvarila za noto, ki je pod kazalnikom.</div>
                        <div class="helpView__text-line specialA1">Če pritisneš na noto, se bo kazalnik premaknil nanjo</div>
                    </div>

                    <!-- MOVEMENT -->
                    <div class="helpView__view view-4" v-if="view == 4">

                        <h2>Natančnejše premikanje med notami</h2>
                        <div class="helpView__text-line">Gumb s puščicama prikaže dva nova gumba, s katerima se lahko bolj natančno premikaš med notami:</div>
                        <div class="helpView__buttons">
                            <sexy-button text="< >" color="orange" customClass="moveButtonsButton" />
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            
                            <sexy-button text="<" color="orange" customClass="moveButtonsButton" />
                            <sexy-button text=". u"  color="green" customClass="musisync revertMovingButtons" />
                            <sexy-button text=">" color="orange" customClass="moveButtonsButton" />
                        </div>
                        <div class="helpView__text-line">Če pritisneš srednji gumb <sexy-button text=". u"  color="green" customClass="musisync revertMovingButtons" />, se gumba za premikanje skrijeta.</div>
                        <div class="helpView__text-line">Z gumboma za premikanje se lahko premakneš tudi čisto na začetek črtovja.</div>

                    </div>

                    <!-- SPECIAL BUTTONS -->
                    <div class="helpView__view view-5" v-if="view == 5">

                        <h2>Posebni gumbi, metronom</h2>
                        <div class="helpView__text-line">Če želiš znova predvajati vajo, pritisni ta gumb:</div>
                        <div class="helpView__buttons">
                            <sexy-button color="sunglow"><div class="tiny-tajni-pici-mici-font">Ponovi<br>vajo</div></sexy-button>
                        </div>
                        <div class="helpView__text-line">Večkrat kot predvajaš vajo, manj točk dobiš.</div>
                        
                        <div class="helpView__text-line">Če pritisneš ta gumb, se pojavijo novi gumbi, s katerimi lahko nastaviš tempo in vključiš ali izključiš metronom.</div>
                        <div class="helpView__text-line">
                            <sexy-button color="sunglow"><icon name="cog" scale="2" /></sexy-button>
                        </div>

                        <div class="helpView__text-line">Z gumboma + in - povečaš in zmanjšaš tempo.</div>
                        <div class="helpView__text-line">
                            
                            <sexy-button color="sunglow">
                                <icon name="minus" />
                            </sexy-button>

                            <b-p-m-text-button text="60" />

                            <sexy-button color="sunglow">
                                <icon name="plus" />
                            </sexy-button>
                        </div>
                        <div class="helpView__text-line">Ko nastaviš tempo, pritisni gumb s kljukico.</div>
                        <div class="helpView__text-line">
                            <sexy-button color="green" >
                                <icon name="check" scale="2" />
                            </sexy-button>
                        </div>


                        <div class="helpView__text-line">S tem gumbom izključiš ali vključiš metronom.</div>
                        <div class="helpView__text-line">
                            <sexy-button color="cabaret">
                                <div class="normal-font">IZKLJUČI METRO<br>NOM</div>
                            </sexy-button>

                            <sexy-button color="sunglow">
                                <div class="normal-font">VKLJUČI METRO<br>NOM</div>
                            </sexy-button>
                        </div>

                        

                    </div>

                    <!-- CHECK -->
                    <div class="helpView__view view-6" v-if="view == 6">

                        <div class="helpView__text-line">Ko se ti zdi, da je vaja pravilno vpisana, pritisni gumb Preveri.</div>
                        <div class="helpView__buttons">
                            <sexy-button color="cabaret"><div class="tiny-tajni-pici-mici-font">Preveri</div></sexy-button>
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            
                            <sexy-button color="red">
                                <icon name="times"/>
                            </sexy-button>

                            <sexy-button color="green">
                                <icon name="check"/>
                            </sexy-button>

                            <sexy-button color="sunglow">
                                <div class="tiny-tajni-pici-mici-font">Naprej</div>
                            </sexy-button>
                        </div>
                        <div class="helpView__text-line">Gumb se spreminja glede na to, ali je vaja pravilna.</div>
                        <div class="helpView__text-line">Če je vaja <span style='color: red;'>narobe</span> vpisana, se za kratek trenutek prikaže križec, nato pa znova napis Preveri.</div>
                        <div class="helpView__text-line">Če je vaja <span style='color: green;'>pravilno</span> vpisana, se za kratek trenutek prikaže kljukica, nato pa napis Naprej.</div>
                        <div class="helpView__text-line">Za nadaljevanje moraš znova pritisniti gumb Naprej.</div>
                    
                    </div>

                    <!-- SELECTION -->
                    <div class="helpView__view view-7" v-if="view == 7">

                        <h2>Izbira več not</h2>
                        <div class="helpView__text-line">Če želiš ustvariti poddelitev, moraš izbrati note, nad katerimi se bo ta izrisala. To narediš z gumbom za izbiro:</div>
                        <div class="helpView__buttons">
                            <selection-button color="green" />
                            <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                            <selection-button color="blue" />
                        </div>
                        <div class="helpView__text-line">Gumb se bo obarval vijolično, na črtovju se bo prikazal pravokotnik s poudarjenima robovoma.</div>
                        <div class="helpView__text-line">Označila se bo prva nota pred kazalnikom. Potem lahko pritisneš na katerokoli drugo noto.</div>
                        <div class="helpView__cursor musisync">
                            <div class="helpView__cursor__lines">&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;</div>
                            <div class="helpView__cursor__notes helpView__cursor__centeredNotes">qqq</div>
                            <div class="helpView__cursor__selection">&nbsp;</div>
                            <div class="helpView__cursor__selection_bar helpView__cursor__selection_leftBar">&nbsp;</div>
                            <div class="helpView__cursor__selection_bar helpView__cursor__selection_rightBar">&nbsp;</div>
                        </div>
                        <div class="helpView__text-line specialA1">Izbira se bo vedno raztezala med prvo izbrano noto in tisto, ki jo pritisneš.</div>
                        <div class="helpView__text-line">Če izbereš napačno začetno noto, spet pritisni gumb za izbiro in ponovi postopek.</div>

                    </div>
                    <!-- TUPLETS -->
                    <div class="helpView__view view-8" v-if="view == 8">

                        <h2>Nepravilne poddelitve / "ole"</h2>
                        <div class="helpView__text-line">Ko imaš izbrane note, se v prvi vrsti pokažejo novi gumbi.</div>
                        <div class="helpView__buttons">
                            <sexy-button color="green"><TupletSign num="3" bg="green" /></sexy-button>
                            <sexy-button color="green"><TupletSign num="2" bg="green" /></sexy-button>
                            <sexy-button color="green"><TupletSign num="5:4" bg="green" /></sexy-button>
                            <sexy-button color="green"><TupletSign num="6:4" bg="green" /></sexy-button>
                        </div>
                        <div class="helpView__text-line">Ko pritisneš na enega izmed njih, se iz izbranih not ustvari izbrana poddelitev.</div>
                        <div class="helpView__text-line">Gumb s trojko ustvari triolo, gumb z dvojko ustvari duolo.</div>
                        <div class="helpView__text-line">5:4 pomeni, da se bo ustvarila poddelitev nad petimi notami v času štirih.</div>
                        <div class="helpView__text-line">Če želene vrste poddelitve ni med gumbi, pritisni ta gumb:</div>
                        <div class="helpView__buttons">
                            <sexy-button color="green"><TupletSign num="?:?" bg="green" /></sexy-button>
                        </div>
                        <div class="helpView__text-line">Program te bo vprašal, kakšno poddelitev želiš ustvariti.</div>
                        <div class="helpView__text-line">Ne preverja se, ali je triola smiselna, za to moraš poskrbeti sam/a 🙂💪</div>
                    </div>

                    <!-- TUPLET DELETE -->
                    <div class="helpView__view view-9" v-if="view == 9">                        
                        
                        <h2>Brisanje poddelitev</h2>
                        <div class="helpView__text-line">Če se zmotiš in ustvariš napačno poddelitev, ali pa jo narediš nad napačnimi notami, jo lahko pobrišeš.</div>
                        <div class="helpView__text-line">Lahko se premakneš do note, ki jo veže znak za poddelitve in jo izbrišeš. Poleg note se bo izbrisala tudi poddelitev.</div>
                        <div class="helpView__text-line">Lahko pa spet označiš poddelitev, ki je ne želiš, in pritisneš ta gumb:</div>
                        <div class="helpView__buttons">
                            <sexy-button color="cabaret">
                                <span class="musisync">T</span>
                                <icon name="ban" scale="2" class="alert"></icon>
                            </sexy-button>
                        </div>
                        <div class="helpView__text-line">S tem gumbom izbrišeš vse poddelitve, ki so v izbirnem pravokotniku. Tudi če označiš samo del poddelitve, se bo izbrisala cela.</div>
                    </div>

                    <!-- SPECIAL 2 -->
                    <div class="helpView__view view-5" v-if="view == 10">

                        <h2>Pomožni gumbi</h2>
                        <div class="helpView__text-line">Če opaziš, da v programu kaj ne deluje ali če se ti zdi da je bila vaja pretežka, pritisni ta gumb in napiši sporočilo.</div>
                        <div class="helpView__buttons">
                            <sexy-button color="cabaret"><div class="tiny-tajni-pici-mici-font">Povratne<br>informacije</div></sexy-button>
                        </div>

                        <div class="helpView__text-line">Če imaš manjši telefon in ti zmanjkuje prostora, lahko pritisneš ta gumb, s katerim skriješ zgornji rumeni meni.</div>
                        <div class="helpView__buttons">
                            <sexy-button ><div class="tiny-tajni-pici-mici-font">Odpri<br>zapri<br>meni</div></sexy-button>
                        </div>


                        <div class="helpView__text-line">Če se ti zdi, da nikakor ne moreš rešiti te vaje, lahko pritisneš gumb vdam se. Ta ti bo takoj pokazal, kakšna je bila vaja. Žal pa za to ne dobiš pozitivnih točk.</div>
                        <div class="helpView__text-line">
                            <sexy-button color="cabaret"><div class="tiny-tajni-pici-mici-font">Vdam<br>se</div></sexy-button>
                        </div>

                        
                    </div>

                </div>
            </div>

        </div>

        <div class="helpView__bottom-nav">
            <div class="helpView__bottomNav__button helpView__bottomNav__closeButton" @click="doHide()">Zapri</div>
            <div class="helpView__bottomNav__button helpView__bottomNav__closeButton" v-bind:class="{ dimButton: view == numViews}" @click="nextView">Naprej</div>
            
            <div class="helpView__bottomNav__button helpView__bottomNav__closeButton" v-bind:class="{ dimButton: view == 1 }"  @click="prevView">Nazaj</div>
        </div>

    </div>

</template>

<style scoped>

    .dimButton {
        opacity: 0.5;
    }

    .helpView{
        
        display: block;
        position: fixed;
        top: 2.5%;
        left: 2.5%;
        background: #FDBB2F;
        width: 95%;
        height: 95%;
        z-index: 10;
        border-radius: 5px;
        padding: 20px;
        overflow-y: scroll;
        -webkit-overflow-scrolling: touch;
        overflow-scrolling: touch;
    }

    .tie-text {
        font-size: 131px;
        margin-bottom: 115px;
        margin-left: 7px;
    }

    .specialA1 {
        padding-top: 26px !important;
    }

    .helpView__bottomNav__button {
        display: inline-block;
        margin-left: 30px;
        background: rgba(255,255,255,0.6);
        padding: 5px;
        color: black;
        cursor: pointer;
    }

    .helpView__bottomNav__button:active {
        transform: scale(0.95) translate(0px, 5px);
    }

    .helpView__buttons-center .button {
        left: 50%;
    }

    .helpView__buttons-center {
        position: relative;
    }

    .helpView__bottom-nav {
        display: block;
        position: fixed;
        bottom: 0px;
        left: 0px;
        background: darkseagreen;
        z-index: 100;
        width: 100%;
        height: 40px;
        padding: 10px;
    }

    .little-padding {
        padding-bottom: 22px;
    }

    .helpView__cursor {
        position: relative;
        width: 100px;
        height: 70px;
    }

    .helpView__cursor div {
        top: 33px;
    }

    .helpView__cursor__lines {
        position: absolute;
        font-size: 80px;
        line-height: 0;
        color: #999999;
        
    }

    .helpView__cursor__cursor{
        position: absolute;
        color: green;
        opacity: 0.5;
        font-size: 108px;
        line-height: 0;
        left: 56px;
    }

    .helpView__cursor__notes {
        position: absolute;
        font-size: 94px;
        line-height: 0;
        top: 36px !important;
    }

    .helpView__cursor__notesBefore {
        color: rgb(255, 94, 0);
        opacity: 0.9;
    }

    .helpView__cursor__notesAfter {
        left: 111px;
        color: maroon;
        opacity: 0.9;
    }

    .helpView__cursor__centeredNotes {
        left: 75px;
    }

    .helpView__cursor__selection{
        position: absolute;
        width: 155px;
        height: 98px;
        background: rgba(0,0,255,0.4);
        top: -6px !important;
        left: 55px;
    }

    .helpView__cursor__selection_bar{
        width: 6px;
        height: 98px;
        top: -6px !important;
        position: absolute;
    }

    .helpView__cursor__selection_leftBar {
        background: rgba(255,0,0,0.6);
        left: 54px;
    }

    .helpView__cursor__selection_rightBar {
        background: rgba(0,0,255,0.6);
        left: 203px;
    }

    .helpView__arrows {
        display: inline-block;
        position: relative;
        font-size: 50px;
        line-height: 0;
        padding: 0 10px 0 10px;
        width: 40px;
        height: 40px;
        overflow: hidden;
    }

    .helpView__arrows__wrap {
        position: absolute;
        bottom: 30px;
        right: 0;
    }

    .helpView__text-line {
        padding: 10px 0 10px 0;
    }

    .norfolk-row .button  {
        font-family: Norfolk;
        font-size: 33px;
    }

    .norfolk {
        font-family: Norfolk !important;
    }

    .musisync-row .button {
        font-family: MusiSync;
        font-size: 33px;
    }

    .musisync {
        font-family: MusiSync !important;
    }

    .norfolk-note-padding{
        padding-top: 19px;
    }

    .moveButtonsButton {
        font-family: initial !important;
        font-size: 26px  !important;
        font-weight: bold;
    }

    .revertMovingButtons {
        font-size: 41px;
        line-height: 0;
        padding: 0px;
    }

    .tiny-tajni-pici-mici-font{
        font-size: 8pt !important;
    }

</style>


<script>

import SexyButton from "../../../elements/SexyButton.vue"
import SexySlider from "../../../elements/SexySlider.vue"
import TupletSign from "../../../elements/TupletSign.vue"

import ThitryTwoButton from "./Buttons/ThirtyTwoButton.vue"
import BarButton from "./Buttons/BarButton.vue"
import DotButton from "./Buttons/DotButton.vue"
import SelectionButton from "./Buttons/SelectionButton.vue"
import BPMSlider from "./Buttons/BPMSlider.vue"
import BPMTextButton from "./Buttons/BPMTextButton.vue"


export default {

    props: [ 'hide' ],

    data() {return {

        TestBPM: {BPM: 0},

        view: 1,
        numViews: 10

    }},

    components: {
        SexyButton, SexySlider, TupletSign,
        
        ThitryTwoButton, BarButton, DotButton, SelectionButton, BPMSlider, BPMTextButton
    },

    mounted() {
    },

    methods: {

        doHide() {

            this.view = 1;

            this.hide();
        },

        prevView() {

            if(this.view > 1){
                this.view --;
            }else {
                this.view = 1;
            }

        },

        nextView() {

            if(this.view < this.numViews){
                this.view++;
            }else {
                this.view = this.numViews;
            }
        }

    }

}
</script>

