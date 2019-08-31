<template>
    <div class="admin__rhythmBars">

        <div class="admin_rhythmBars_masterView" >

            <div class="admin_rhythmBars_masterView_header" >
                <div class="headerPrompt"> Gradbeni elementi ({{allBarsCount}})</div>
                <div class="addNewBarButton" @click="addEmpty()">Dodaj novega</div>
            </div>

            <div ref="barsScroll" class="admin_rhythmBars_masterView_body">
                <RhythmBarInfo v-for="item in bars" ref="renderedBar" v-bind:key="item.id" :info="item" @mousedown.native="barSelected(item)" />
                <div v-if="allPages > currentPage" class="loadMore" @click="loadMore()">Naloži več...</div>
            </div>

            <div class="admin_rhythmBars_masterView_footer">
                <!-- <div class="button1 importMusicXML" style="font-size: 10px;"> -->
                <upload-file text="Uvozi MusicXML" :onFileUploaded="musicXmlImported" />
                <!--<div class="button1 importJSON" style="font-size: 10px;">Uvozi JSON</div>-->
            </div>


        </div>

        <div class="admin_rhythmBars_detailView" >

            
            <div v-if="!selected" class="admin_rhythmBars_detailView_selectPrompt" >
                Izberi element na levi
            </div>

            <div v-if="selected">
                <div class="admin_rhythmBars_detailView_header" >
                    <div class="" style="display: inline-block;">Urejanje elementa <span class='normalfont'>#{{selected.id ? selected.id : "Nov takt"}}.</span></div>
                    <!--<div class="button1" @click="downloadJSON" >Prenesi JSON</div>-->
                </div>

                <div class="admin_rhythmBars_detailView_header_content">

                    <StaffView ref="staff_view" :bar="barInfo" :hideTimeSignatures="true" :enabledContexts="['zoomview']">
                        <div class="admin__rhythmBarInfo__barInfoDetail__staffView_container">
                            <div class="admin__rhythmBarInfo__barInfoDetail__staffView" id="detailViewStaffView"></div>
                        </div>
                    </StaffView>

                    <RhythmKeyboard ref="keyboard" :key_callback="key_callback" :buttonState="buttonState" :selected="selected" />

                    <div id="table" class="col-xs-12 table-responsive">
                        <datatable :columns="columns" :data="rows">

                            <template scope="{ row }">
                                <tr>
                                    <td>{{ row.feature.name }}</td>
                                    <td>Verjetnost: <input type="text" v-model="row.bar_probability" /></td>
                                    <td>
                                        <button class="btn btn-xs btn-primary" @click="alert('Will delete the bar now,')">
                                            <icon name="trash" />
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </datatable>
                    </div>

                </div>
            </div>

        </div>

    </div>
</template>

<script>

import { mapState, mapActions } from 'vuex'
import RhythmBarInfo from './AdminRhythmBarInfo.vue'
import StaffView from '../games/rhythm/StaffView.vue'
import RhythmKeyboard from '../games/rhythm/Keyboard/AdminKeyboard.vue'

import NoteStore from "../games/rhythm/noteStore"
import UploadFile from "./UploadFile.vue"

import DatatableFactory from 'vuejs-datatable';

let Fraction = require('fraction.js');

export default {
    
    data() {
        return {

            columns: [
                { label: 'ID', field: 'id', align: 'center', filterable: false },
                { label: 'Username', field: 'user.username' },
                { label: 'First Name', field: 'user.first_name' },
                { label: 'Last Name', field: 'user.last_name' },
                { label: 'Email', field: 'user.email', align: 'right', sortable: false }
            ],
            rows: [
                {id: 1, user: {username: "Fred", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 2, user: {username: "JAKUB", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 3, user: {username: "Vrenje", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 4, user: {username: "Moagel", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 4, user: {username: "Moagel", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 4, user: {username: "Moagel", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 4, user: {username: "Moagel", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
                {id: 4, user: {username: "Moagel", first_name: "Blagoš", last_name: "Vikram", email: "blagoš.vikram@medd.gr"}},
            ],

            currentPage: 1,
            allPages: null,
            allBarsCount: "nalaganje...",
            bars: [],

            files: [],

            notes: [],

            selected: null,

            cursor: {
                position: 0
            },

            buttonState: {
                save: 'normal', // 'loading' | 'done'
                deleteBar: 'normal' // 'loading' | 'done'
            },

            initialized: false
        }
    },

    components: {
        RhythmBarInfo, StaffView, RhythmKeyboard, UploadFile, DatatableFactory
    },

    computed: {
        
        barInfo() {
            if(this.selected)
                return this.selected.timeSignature;

            return null;
        }

    },

    
    methods: {
        ...mapActions(['fetchRhythmBars', 'deleteRhythmBar', 'saveRhythmBar', 'createRhythmBar']),

        takt(a) {
            if(a.subdivisions){
                return a.subdivisions.map((k) => {return k.n +"/"+ k.d}).join("+");
            }

            return a.num_beats +"/"+ a.base_note;
        },

        getBarIdx(id){
            let vId = -1;
            for(let i = 0; i < this.bars.length && vId < 0; i++){
                if(this.bars[i].id == id){
                    vId = i;
                }
            }
            return vId;
        },

        removeBarFromList(id){
            
            let vId = this.getBarIdx(id);
            if(vId > -1){ 
                this.bars.splice(vId, 1);
            }
            
        },

        replaceBarInList(bar){

            let idx = this.getBarIdx(bar.id);
            this.$refs.renderedBar[idx].render();

        },

        addBarToList(bar, idx){

            if(typeof idx == "undefined"){
                idx = this.bars.length - 1;
            }

            this.bars.splice(idx, 0, bar);
            let scE = this.$refs.barsScroll.el;
            scE.scrollTo(0, scE.scrollHeight);
            
        },

        downloadJSON() {

            this.downloadFile(
                "takt"+this.selected.id+".json", 
                JSON.stringify(this.notes.notes)
            );

        },

        downloadMusicXml() {

        },

        downloadFile(filename, content) {

            var element = document.createElement('a');
            element.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(content));
            element.setAttribute('download', filename);

            element.style.display = 'none';
            document.body.appendChild(element);

            element.click();

            document.body.removeChild(element);

        },

        musicXmlImported(res){

            let imported = res.imported;
            alert("Uvoženi so bili takti s številkami\n"+JSON.stringify(imported));
            this.reload();

        },

        loadBarsPage() {

            let out = this;

            return out.fetchRhythmBars({pageNum: out.currentPage}).then((res) => {

                if(res.last_page){
                    out.allPages = res.last_page;
                }

                if(res.total){
                    out.allBarsCount = res.total;
                }

                if(res.current_page){
                    out.currentPage = res.current_page;
                }

                if(res.data && res.data.length >= 0){
                    out.processBars(res.data);
                }

                if(out.allPages > res.current_page){
                    out.allBarsCount = "nalaganje...";
                    return out.loadMore();
                }else {
                    return out.allBarsCount = res.total;
                }

            });
        },

        loadMore() {
            this.currentPage++;
            return this.loadBarsPage();
        },

        reload(){

            this.currentPage = 1;
            this.allPages = null;
            this.allBarsCount = "nalaganje...";
            this.bars = [];

            this.files = [];

            return this.loadBarsPage();
        },

        processBars(barsData) {

            // parse JSON bars
            for(let i = 0; i < barsData.length; i++){
                debugger;
                barsData[i].content = JSON.parse(barsData[i].content);
                barsData[i].timeSignature = {base_note:4, num_beats:4};
            }
            this.bars = this.bars.concat(barsData);
        },

        addEmpty() {

            this.barSelected({
                content: [],
                timeSignature: {num_beats: 4, base_note: 4}
            });

        },

        barSelected(item) {

            if(this.selected && item.id == this.selected.id) 
                return;
            
            
            this.selected = item;

            this.$nextTick(() => {

                if(!this.initialized){
                    this.initialized = true;

                    this.$refs.staff_view.CTX.zoomview.id = 'detailViewStaffView';
                    this.$refs.staff_view.init({cursor:{enabled: true}});
                    this.$refs.keyboard.init(this.$refs.staff_view.cursor);
                }

                //debugger;
                // TODO TUKAJ SI OSTAL: ne dela, ko pritisneš na bar na levi. Zrihtaj da se da pritisnit, potem uredi še urejanje kategorij in pripadnosti kategorijam.
                // Initialize note store
                this.notes = new NoteStore(
                    this.selected.timeSignature,
                    this.$refs.staff_view.cursor,
                    this.$refs.staff_view.render
                );
                this.notes.notes = this.selected.content;
                this.notes._call_render();

            });
        },

        key_callback(event) {

            if(event.type == "selectionMode"){

                this.$refs.staff_view.toggleSelectionMode();
                this.notes._call_render();
            }
            else if(event.type == "showJson"){

                // Replacements:
                // 1.   },    ->  },\n\t\t\t
                // 2. ({"type":"bar".*},)    ->   \n\n\t\t\t$1\n\n
                // 3.   ,"    ->   , "

                // let text = JSON.stringify(this.notes.notes)
                //     .replace(/\[/, "\t\t\t")
                //     .replace(/\]/, "")
                //     .replace(/},/gi, "},\n\t\t\t")
                //     .replace(/({"type":"bar".*},)/gi, "\n\n\t\t\t$1\n\n")
                //     .replace(/":/gi, "\" :")
                //     .replace(/,"/gi, ", \"")
                //     .replace(/"/gi, "'");

                let utils = require('../games/rhythm/rhythmUtilities');

debugger;
                let text = JSON.stringify(this.notes.notes)
                       .replace(/\[/, "")
                       .replace(/\]/, "")
                       .replace(/"/gi, "'");
                text = "(   ,"+"\""+text+"\""+", "+utils.getNotesDuration(this.notes.notes)+")";

                console.log(text);

            }
            else if(event.type == "showHelp") {
                this.showHelp = true;
            }
            else if(event.type == "changeSignature"){
                
                let heh = prompt("Vnesite taktovski način v obliki: ?/?. Recimo 4/4 ali 3/4\n\n Mešani taktovski način vnesite v obliki ?/?+?/?+... recimo 3/4+2/4");
                
                let subdivisions = heh.split("+");
                
                let sum = new Fraction(0);

                if(subdivisions.length == 0) return;
                
                let s = subdivisions.map(s => {
                    let m = s.split("/");
                    let n = parseInt(m[0]);
                    let d = parseInt(m[1]);
                    
                    if(!n || !d) return null;
                    sum = sum.add(new Fraction(n,d));
                    
                    return {n: n, d:d};
                });

                if(subdivisions.length == 1){
                    this.barInfo.base_note = s[0].d;
                    this.barInfo.num_beats = s[0].n;
                    delete this.barInfo.subdivisions;
                }
                else{
                    this.barInfo.subdivisions = s;
                    this.barInfo.base_note = sum.d;
                    this.barInfo.num_beats = sum.n;
                }

                this.notes._call_render();
            }
            else if(event.type == "saveBar") {

                this.saveBar();

            }
            else if(event.type == "deleteBar") {
                
                this.deleteBar();

            }
            else if(event.type == "changeDifficulty") {
                this.askForDifficulty();
            }
            else{
                this.notes.handle_button(event);
            }

        },

        askForDifficulty(){

            let num = prompt("Vnesite težavnost takta v številki (nekaj vmes med 50 in 350) \n50 - lahek\n100 - srednje težak\n300 - zelo težak...");
                if(!num) return false;

                num = parseInt(num);
                if(num < 50 || num > 300){
                    alert("Težavnost mora biti med 50 in 300");
                    return false;
                }

                this.selected.difficulty = num;
                return true;

        },

        saveBar() {

            this.buttonState.save = "loading";

            let out = this;

            let obj = {
                content: JSON.stringify(this.selected.content),
            };

            if(this.selected.id){
                obj.id = this.selected.id;
                this.saveRhythmBar({bar: obj}).then(bar => {
                    this.buttonState.save = "normal";
                    
                    this.replaceBarInList(this.selected);
                }).catch(() => {
                    this.buttonState.save = "error";
                });    
            }else {
                this.createRhythmBar({bar: obj}).then(bar => {
                    out.selected.id = bar.id;
                    out.buttonState.save = "normal";
                    out.addBarToList(this.selected);
                }).catch(() => {
                    out.buttonState.save = "error";
                    return out.reload();
                });
            }

        },

        deleteBar() {

            this.buttonState.deleteBar = "loading";
            let out = this;

            if(confirm("Ali res želite izbrisati ta takt?")){
                
                let id = this.selected.id;
                out.removeBarFromList(id);
                this.selected = null;
                this.initialized = false;
                
                this.deleteRhythmBar({id: id}).then((k) => {
                    
                    this.buttonState.deleteBar = "normal";
                })
                .catch((error) => {
                    console.error(error);
                    //this.buttonState.deleteBar = "error";
                    debugger;
                    let message = error.message;
                    if(error.response && error.response.data && error.response.data.message){
                        message = error.response.data.message;
                    }
                    alert("Napaka! Ni mi uspelo izbrisati takta...\n\nPojasnilo: "+message);
                    
                    return out.reload();
                });
            }

            
        }
    },

    mounted() {

        let vue = this;
        window.onresize = function(event) {
            vue.$refs.staff_view.viewportResized();
        }

        window.onorientationchange = function(event) {
            vue.$refs.staff_view.viewportResized();
        }


        this.loadBarsPage();

    }

}
</script>

<style lang="scss" scoped>


    @import '../../../sass/variables/index';

    .addNewBarButton, .button1 {
        background: #f7cab1;
        font-family: $font-regular;
        display: inline-block;
        padding: 5px;
        text-align: right;
        cursor: pointer;
        height: 30px;
    }

    .addNewBarButton:hover, .button1:hover {
        background: lighten($color: #f7cab1, $amount: 10) ;
        
    }

    .normalfont {
        font-family: $font-regular;
    }

    .admin__rhythmBars {
        display: flex;
        flex-direction: row;
        height: calc(100vh - 70px);
    }

    .admin_rhythmBars_masterView {
        width: 30%;
        height: 100%;
    }

    .admin_rhythmBars_masterView_header {
        width: 100%;
        height: 60px;
        background: $jaffa;
        padding: 20px 0 0 10px;
    }

    .admin_rhythmBars_masterView_footer {
        height: 40px;
        background: $jaffa;
        padding: 6px;
    }
    
    .admin_rhythmBars_masterView_body {
        // 100% - footer - header
        height: calc(100% - 40px - 60px);
        overflow-y: auto;
        overflow-x: hidden;
        background: $jaffa;
    }

    .admin__rhythmBarInfo__barInfoEntry:nth-child(even) {
        background: $jaffa;
    }

    .admin__rhythmBarInfo__barInfoEntry:nth-child(odd) {
        background: $sandy-brown;
    }

    .admin_rhythmBars_detailView {
        width: 100%;
        height: 100%;
        background: $tacao;
        overflow: hidden;
    }

    .admin_rhythmBars_detailView_header {
        height: 60px;
        background: #EB7D3D;
        padding: 20px 0 0 10px;
    }

    .admin__rhythmBarInfo__barInfoDetail__staffView {
        //padding-top: 50px;
        -webkit-transform: scale(2) translate(25%, 25%);
        transform: scale(2) translate(25%, 25%);
    }

    .admin__rhythmBarInfo__barInfoDetail__staffView_container {
        padding-bottom: 79px;
        overflow-x: scroll;
        margin-bottom: 45px;
    }

    .admin_rhythmBars_detailView_selectPrompt {
        width: 100%;
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .headerPrompt {
        display: inline-block;
        margin-right: 20px;
    }

    .addNewBarButton {
    }

    .loadMore {
        padding: 20px 0 20px 0;
        text-align: center;
        background: $sandy-brown;
    }

    .loadMore:hover {
        background: lighten($color: $sandy-brown, $amount: 10);
        cursor: pointer;
    }

</style>


