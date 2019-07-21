<template>

    <div class="helpView__wrap">

        <div class="helpView">

            <h1>Pomoč</h1>
            <div class="row">
                <div class="col-md-6">

                    <div class="helpView__text-line">S pritiskom na enega od teh gumbov dodaš noto na črtovje:</div>
                    <div class="helpView__buttons norfolk-row">
                        <sexy-button color="green"><div class="norfolk-note-padding">&#x0068;</div></sexy-button>
                        <sexy-button color="green"><div class="norfolk-note-padding">&#x0071;</div></sexy-button>
                        <sexy-button color="green"><div class="norfolk-note-padding">&#x0065;</div></sexy-button>
                        <sexy-button color="green"><div class="norfolk-note-padding">&#x0078;</div></sexy-button>
                    </div>

                    <div class="helpView__text-line">s temi pa dodaš pavze:</div>
                    <div class="helpView__buttons norfolk-row">
                        <sexy-button color="red">&#x0152;</sexy-button>
                        <sexy-button color="red">&#x2030;</sexy-button>
                        <sexy-button color="red">&#x2248;</sexy-button>
                        <sexy-button color="red">&#x00AE;</sexy-button>
                    </div>

                    <div class="helpView__text-line">Ta gumb doda taktnico:</div>
                    <div class="helpView__buttons musisync-row">
                        <bar-button />
                    </div>

                    <div class="helpView__text-line">Ta pa piko na noto za kazalnikom:</div>
                    <div class="helpView__buttons musisync-row">
                        <sexy-button text="." color="green"/>
                    </div>

                    <div class="helpView__text-line">Če pritisneš na ta gumb, povežeš zadnji dve noti pred kazalnikom z vezajem:</div>
                    <div class="helpView__buttons musisync-row">
                        <sexy-button text="u" color="green"/>
                    </div>

                    <div class="helpView__text-line">Če se zmotiš, lahko noto pobrišeš s tem gumbom:</div>
                    <div class="helpView__buttons musisync-row">
                        <sexy-button color="cabaret">
                            <img src="/images/backspace.svg" width="30" />
                        </sexy-button>
                    </div>

                    <h2>Kazalnik</h2>
                    <div class="helpView__text-line">Zelena pokončna črta na notnem črtovju je kazalnik. Pokaže ti, kam bo napisana naslednja nota, ki jo boš dodal.</div>
                    <div class="helpView__cursor musisync">
                        <div class="helpView__cursor__lines">&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;&#x00A9;</div>
                        <div class="helpView__cursor__notes helpView__cursor__notesBefore">qq</div>
                        <div class="helpView__cursor__cursor">&#x2019;</div>
                        <div class="helpView__cursor__notes helpView__cursor__notesAfter">qq</div>
                    </div>
                    <div class="helpView__text-line">Oranžne note so note pred kazalnikom, tiste rdeče pa so note za kazalnikom.</div>
                    <div class="helpView__text-line">Če pritisneš na noto na črtovju, se bo kazalnik premaknil za njo, tako da bo sama postala nota pred kazalnikom.</div>
                    <div class="helpView__text-line">Brišeš lahko le note pred kazalnikom.</div>

                    <h2>Premikanje med notami</h2>
                    <div class="helpView__text-line">Gumb s puščicama prikaže dva nova gumba za premikanje med notami:</div>
                    <div class="helpView__buttons">
                        <sexy-button text="< >" color="orange" customClass="moveButtonsButton" />
                        <div class="helpView__arrows helpView__arrowRight"><div class="helpView__arrows__wrap">&rarr;</div></div>
                        
                        <sexy-button text="<" color="orange" customClass="moveButtonsButton" />
                        <sexy-button text=". u"  color="green" customClass="musisync revertMovingButtons" />
                        <sexy-button text=">" color="orange" customClass="moveButtonsButton" />
                    </div>
                    <div class="helpView__text-line">Če nato pritisneš srednji gumb <sexy-button text=". u"  color="green" customClass="musisync revertMovingButtons" />, potem se gumbi za premikanje skrijejo.</div>
                    <div class="helpView__text-line">Če se želiš premakniti čisto na začetek črtovja, lahko to narediš s temi gumbi.</div>

                    <h2>Posebni gumbi</h2>
                    <div class="helpView__text-line">Če želiš znova predvajati vajo, pritisni ta gumb:</div>
                    <div class="helpView__buttons">
                        <sexy-button color="sunglow"><div class="tiny-tajni-pici-mici-font">Ponovi vajo</div></sexy-button>
                    </div>
                    <div class="helpView__text-line">Pazi: cilj te igre je utrjevanje ritmičnega spomina in s tem čim manjkrat poslušati vajo, zato ti ta gumb zmanjšuje točke.</div>

                    <div class="helpView__text-line">
                        S tem gumbom spreminjaš hitrost predvajanja. 
                        Dotakni se ga in podrsaj levo ali desno.
                        Lahko poskusiš na tem spodnjem gumbu.
                    </div>
                    <div class="helpView__buttons helpView__buttons-center">
                        <sexy-slider color="cabaret" :value="TestBPM" valueKey="BPM" :from="20" :to="250">
                            <div class="BPM-indicator normal-font">
                                <div class="BPM-value">{{TestBPM.BPM}}</div>
                                <div class="BPM-prompt">BPM</div>
                            </div>
                        
                        </sexy-slider>
                    </div>

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
                    <div class="helpView__text-line">Če je vaja napačna, se za kratek trenutek prikaže križec, nato pa znova napis Preveri.</div>
                    <div class="helpView__text-line">Če je vaja pravilna, se za kratek trenutek prikaže kljukica, nato pa napis Naprej.</div>
                    <div class="helpView__text-line">Za nadaljevanje moraš znova pritisniti gumb Naprej.</div>
                    

                </div>
            </div>

        </div>

        <div class="helpView__bottom-nav">
            <div class="helpView__bottomNav__closeButton" @click="hide">Zapri</div>
        </div>

    </div>

</template>

<style>

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
        left: 87px;
    }

    .helpView__cursor__notes {
        position: absolute;
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

import ThitryTwoButton from "./Buttons/ThirtyTwoButton.vue"
import BarButton from "./Buttons/BarButton.vue"


export default {

    props: [ 'hide' ],

    data() {return {

        TestBPM: {BPM: 0}

    }},

    components: {
        SexyButton, ThitryTwoButton, BarButton, SexySlider
    }

}
</script>

