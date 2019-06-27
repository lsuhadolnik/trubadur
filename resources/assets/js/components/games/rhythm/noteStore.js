//import { util } from "node-forge";

var Fraction = require("fraction.js");
let util = require('./rhythmUtilities');

var NoteStore = function(bar, cursor, render_function, info) {

    // The supported note durations.
    // Currently supports up to a sixteenth note with a dot.
    this.supportedLengths = [1, 2, 4, 8, 16, 32];
    this.supportedRests   = [4, 8, 12, 16, 32];


    this.bar = bar;
    this.cursor = cursor;
    this.notes = [];

    this.getClearTupletInfo = function(){
        return {
            length : 0,
            type: 0
        }
    }
    this.currentTupletInfo = this.getClearTupletInfo();
    

    this._call_render = function(){

        render_function(
            this.notes,
            this.cursor
        );

    }

    // Init notes with default
    // Initially the view is empty
    // This line is also responsible for rendering the empty bars on load
    this._call_render();
    

    this.handle_button = function(event) {

        if(this.cursor.editing_tuplet){
            this.tupletEditing_buttonHandler(event);
            return;
        }

        if(event.type == 'n' || event.type == 'r' || event.type == 'bar')
        {   
            this.add_note(event);
        } 
        else if(event.type == 'dot')
        {
            this.add_dot();
        }
        else if(event.type == 'tie')
        {
            this.add_tie();
        }
        else if(event.type == 'delete')
        {   
            this.delete_note();
        }
        else if(event.type == '>')
        {
            this._move_cursor_forward();
        }
        else if(event.type == '<')
        {
            this._move_cursor_backwards();
        }
        else if(event.type == 'tuplet')
        {
            // this.enable_tuplet_editing(event);
            this.tupletCreation_applyTupletToSelection(event);
            this.cursor.clearSelection();
        }

        else if(event.type == "remove_tuplets") 
        {
            this.tupletEditing_removeInSelection();
            this.cursor.clearSelection();
        }

        this._call_render();

    }

    this.clear = function(){
        
        this.notes = [];
        this.cursor.position = 0;
        this.cursor.editing_tuplet = false;
        
        this.currentTupletInfo = this.getClearTupletInfo();
        
        this._call_render();

    }

    this.tupletEditing_removeInSelection = function(){

        let sel = this.cursor.selection;

        // If in the middle of a tuplet; Delete it...
        this.clearTupletBackwards(sel.from);
        this.clearTupletForwards(sel.to);

        for (let i = sel.from; i < sel.to + 1 && i < this.notes.length; i++) {
            const note = this.notes[i];
            this.clearTupletNote(note);
        }

    }

    this.selectionFunctions_iterate = function(f){

        if(!f || typeof f !== "function") return;

        let sel = this.cursor.selection;
        for(let i = sel.from; i < sel.to + 1 && i < this.notes.length; i++){
            const note = this.notes[i];
            let res = f(note);
            if(typeof res !== "undefined"){
                // If the function returns something, break out of it
                return res;
            }
        }

        return false;

    }

    this.selectionFunctions_checkIfCrossBar = function(){

        return this.selectionFunctions_iterate(note => {
            if(note.type == "bar") return true;
        });

    }

    this.selectionFunctions_delete = function(){

        let sel = this.cursor.selection;
        this.tupletEditing_removeInSelection();
        this.notes.splice(sel.from, sel.to - sel.from + 1);

        this.selectionFunctions_moveCursorLeftToSelection();
        this.cursor.clearSelection();

    }

    this.selectionFunctions_moveCursorLeftToSelection = function() {

        let sel = this.cursor.selection;

        //
        //  Move cursor
        //
        let pos = this.cursor.position;
        let dif = 0;
        // Inside
        if(pos <= sel.to && pos >= sel.from){

            // Move left for sel.from - pos (right the opposite)
            dif = pos - sel.from;
        }
        // Selection if before the cursor
        else if(pos > sel.to){
            // Move left for selection length
            dif = -(sel.to - sel.from + 1);
        }
        // Selection if after the cursor
        else if (pos < sel.from){
            // Stay where you are.
        }
        this.cursor.position += dif;

    }

    this.tupletCreation_applyTupletToSelection = function(event){

        if(this.selectionFunctions_checkIfCrossBar()){
            alert("Ne morem narediti triole čez takt.");
            return;
        }

        if(!event.tuplet_type){
            event.tuplet_type = parseInt(prompt("Vnesi vrednost triole"));
            if(!event.tuplet_type){ return; }
        }

        this.tupletEditing_removeInSelection();

        let sel = this.cursor.selection;

        this.selectionFunctions_iterate(note => {
            
            note.in_tuplet = true;
        });

        this.notes[sel.to].tuplet_end = true;
        this.notes[sel.to].tuplet_type = event.tuplet_type;

    }

    this.clearTupletNote = function(note){
        delete note.in_tuplet;
        delete note.tuplet_end;
        delete note.tuplet_type;
    }

    this.clearTupletForwards = function(idx){

        if(!this.notes[idx].in_tuplet) return;

        for (let i = idx; i < this.notes.length; i++) {
            const note = this.notes[i];
            
            if(note.tuplet_end){
                this.clearTupletNote(note);
                return;

            }else if(note.in_tuplet){
                this.clearTupletNote(note);
            }

        }

    }

    this.clearTupletBackwards = function(idx){

        if(!this.notes[idx].in_tuplet) return;

        for (let i = idx; i >= 0; i--) {
            const note = this.notes[i];
            
            if(note.tuplet_end){
                this.clearTupletNote(note);
                return;

            }else if(note.in_tuplet){
                this.clearTupletNote(note);
            }

        }

    }

    this.tupletEditing_buttonHandler = function(event) {

        /**
         * How does a tuplet look like:
         * 
         *  ... (n) (n) (n:t1) (n:t2) (n:t3) (n) ...
         * 
         * (n:t1), (n:t2)
         * {
         *      type: "n",
         *      duration: new Fraction(1, duration / tupletType),
         *      symbol: "4",
         *      in_tuplet: true
         * }
         * 
         * (n:t3)
         * {
         *      type: "n",
         *      duration: new Fraction(1, duration / tupletType),
         *      symbol: "4",
         *      in_tuplet: true,
         *      tuplet_end: true
         * }
         * 
         */

        if(event.type == "tuplet"){
            this.cursor.editing_tuplet = false;
        }
        else if(event.type == "n" || event.type == "r"){
            this.tupleEditing_handleNote(event);
            this._call_render();
        }
        else if(event.type == "delete"){
            this.tupletEditing_handleDelete(event);
            this._call_render();
        }
        else {
            alert("This tuplet edit mode feature is not yet implemented");
        }

    }

    this.tupletEditing_handleDelete = function(event) {

        //    prePreviousNote               previousNote
        // 1) ???                       |   xxx                     -> return
        // 2) (in_tuplet)               |   (in_tuplet, tuplet_end) -> delete previousNote, add flag to prePrevious
        // 3) (in_tuplet, tuplet_end)   |   (in_tuplet, tuplet_end) -> delete previousNote, return 
        // 4) (in_tuplet, tuplet_end)   |   (in_tuplet)             -> delete previousNote, return
        // 5) (in_tuplet)               |   (in_tuplet)             -> delete previousNote, return
        // 6) xxx                       |   (in_tuplet, *)          -> delete previousNote, return



        if(this.previousNote()){

            if( this.prePreviousNote()
            &&  this.prePreviousNote().in_tuplet
            && !this.prePreviousNote().tuplet_end
            &&  this.   previousNote().tuplet_end){
                this.prePreviousNote().tuplet_end = true;
            }

            this.deletePreviousNote();

            this._move_cursor_backwards();

        }
        // else return; (1)
        
    }

    /**
     * Tries to get info from the neatest tuplet
     * If there are more tuplets around the cursor, the first is always selected
     */
    this.getCurrentTupletType = function(){

        
        // previousNote            currentNote
        // xxx                     xxx                     -> return null
        // xxx                     ()                      -> return null
        // xxx                     (in_tuplet)             -> get info forwards
        // ()                      (in_tuplet)             -> Get info forwards
        // (in_tuplet)             (in_tuplet)             -> Get info backwards
        // (in_tuplet)             xxx                     -> ERROR
        // (in_tuplet, tuplet_end) ()                      -> Get type from previousNote
        // (in_tuplet, tuplet_end) (in_tuplet)             -> Get type from previousNote
        // (in_tuplet, tuplet_end) (in_tuplet, tuplet_end) -> Get info backwards

        for (let i = this.cursor.position; i >= 0; i--) {
            
            let currentNote  = null;
            if(this.notes.length > i) currentNote = this.notes[i];
            
            let previousNote = null;
            if(this.notes.length > i - 1 && i - 1 >= 0) previousNote = this.notes[i - 1];

            if(currentNote && currentNote.in_tuplet && (!previousNote || !previousNote.in_tuplet)) {
                return util.getNoteType(currentNote);
            }
            
        }

        return 0;

    }

    this.tupleEditing_handleNote = function(event){

        let tuplet_type = this.currentTupletInfo.type;

        if(tuplet_type == 0){

            this.tupletEditing_startNewTuplet(event)

        }else {

            if(tuplet_type > util.getNoteType(event)){

                this.tupletEditing_handleExit(event);
                return;

            }else {
                this.tupletEditing_addToExistingTuplet(event)
            }

        }

        this._move_cursor_forward();


    }

    this.tupletEditing_handleExit = function(event){
        
        // Cancel if in the middle of a tuplet
        if(this.inTheMiddleOfATuplet()){
            alert("To se pa ne da.");
            return;
        }

        // Exit tuplet editing mode and pass request to parent
        this.cursor.editing_tuplet = false;
        this.handle_button(event);

    }

    this.tupletEditing_startNewTuplet = function(event) {
        
        // set currentTupletType
        this.currentTupletInfo.length = 1;
        this.currentTupletInfo.type = event.duration.d;
        // add tuplet note   

        event.in_tuplet = true;
        event.tuplet_end = true;
        this.addNote(event);

    }

    this.tupletEditing_addToExistingTuplet = function(event) {

        // Add tuplet sign
        event.in_tuplet = true;

        // previousNote                currentNote              What to do
        // (in_tuplet, tuplet_end)  |  xxx                       Add tuplet note to currentNote, remove tuplet_end from previousNote
        // (in_tuplet, tuplet_end)  |  ()                        Add tuplet note to currentNote, remove tuplet_end from previousNote
        // (in_tuplet, tuplet_end)  |  (in_tuplet, tuplet_end)   Add tuplet note to currentNote, remove tuplet_end from previousNote
        
        // (in_tuplet)              |  (in_tuplet)               Add tuplet note to currentNote
        // (in_tuplet)              |  (in_tuplet, tuplet_end)   Add tuplet note to currentNote

        // xxx                      |  xxx

        if(this.previousNote()){
            if(this.previousNote().tuplet_end){
                // Set new tuplet end
                event.tuplet_end = true;
                delete this.previousNote().tuplet_end;
            }
        }else { // No previousNote
            // Set new tuplet end
            event.tuplet_end = true;
        }

        // Add new tuplet note
        this.addNote(event);
        
    }

    
    
    this.atTheEndOfATuplet = function(){

        //       previousNote          currentNote
        // (in_tuplet, tuplet_end) | (note)
        // (in_tuplet, tuplet_end) |  xxxx
        // (in_tuplet, tuplet_end) | (in_tuplet)
        // (in_tuplet, tuplet_end) | (in_tuplet, tuplet_end)
        //          xxx            |  xxx

        if(this.previousNote()){
            let n = this.previousNote(); 
            return n.tuplet_end;
        }else{
            return true;
        }
        
    }

    this.inTheMiddleOfATuplet = function(){

        // previousNote     currentNote
        //  (in_tuplet)  | (in_tuplet)
        //  NOO NOO NOO !! -> (in_tuplet, tuplet_end) | (in_tuplet, tuplet_end)

        if(this.previousNote() && this.currentNote()){
            if( this.previousNote().in_tuplet 
            &&  this. currentNote().in_tuplet
            && !this.previousNote().tuplet_end) 
                return true;
        }

        return false;
    }

    this.add_tie = function(){

        let n = this.cursor.position - 1;

        // Don't do anything if this is the first note...
        if(n <= 0) {
            return;
        }

        if(this.notes[n].type == "bar"){
            return;
        }

        this.notes[n].tie = !this.notes[n].tie;
        
    }

    this.add_dot = function() {

        let n = this.cursor.position - 1;

        // Don't do anything if this is the first note...
        if(n <= 0) {
            return;
        }

        if(this.notes[n].type == "bar"){
            return;
        }

        let note = this.notes[n];
        if(note.dot)
        {
            note.duration = note.duration.div(1.5);
            note.dot = false; 
        } 
        else 
        {
            note.duration = note.duration.mul(1.5);
            note.dot = true;    
        }
    

    }

    this.enable_tuplet_editing = function(event){

        this.cursor.editing_tuplet = true;
        this.currentTupletInfo = this.getClearTupletInfo();

    },

    this.remove_tuplet = function(event){

        // Remove tuplet in current position
        // Removes the tuplet even if the cursor is inside

        let i = cursor.position - 1;

        while(this.notes[i] && !this.notes[i].hasOwnProperty('tuplet_end')){
            i++;
        }
        if(!this.notes[i]){
            alert("Nekaj je narobe. Nisem našel zaključka triole... To je napaka v kodi.");
        }

        // i je na zaključku triole
        // Sprehodi se
        delete this.notes[i].tuplet_end;
        do {

            let note = this.notes[i];

            if(note.type == "bar"){
                return;
            }

            delete note.in_tuplet;
            note.duration = note.duration.mul(note.tuplet_type);

            delete note.tuplet_type;
            
            i--;
        } 
        // Odstranjuj, dokler traja ta triola ali ne trčiš ob drugo triolo
        while(i >= 0 && !this.notes[i].tuplet_end && this.notes[i].in_tuplet)


    },

    this.clear_other_tuplet_snap_notes = function(pos){
        for(var i = 0; i < this.notes.length; i++){
            if(i != pos && this.notes[i].was_tuplet_end){
                delete this.notes[i].style;
                delete this.notes[i].was_tuplet_end;
            }
        }
    }

    this.check_if_there_were_any_tuplets_before = function(current_idx, max_dist){

        var potentialTupletEnd = -99999999;
        var potentialDist = Math.abs(potentialTupletEnd - current_idx) 

        for(var i = 0; i < this.notes.length; i++){

            var thisDist = Math.abs(i - current_idx)

            if(this.notes[i].was_tuplet_end && thisDist <= max_dist && thisDist < potentialDist){
                potentialTupletEnd = i;
            }
        }

        if(potentialTupletEnd >= 0){
            return potentialTupletEnd;
        }

        return -1;

    }

    this.add_tuplet = function(event) {

        // Original cursor position
        var n = cursor.position - 1;
        if(n < 0 || this.notes.length < n){
            return;
        }
        
        var lastNote = this.notes[n];
        if(lastNote.type == "bar" || lastNote.in_tuplet){
            return;
        }

        // Check if behind a valid note
        if(["2","2r","4", "4r", "8", "8r", "16", "16r"].indexOf(lastNote.symbol) < 0){
            return; 
        }

        // Delete this note
        lastNote = _.clone(lastNote);
        this.notes.splice(n, 1);

        // New symbol
        let newSymbol = (lastNote.duration.d * 2) + (lastNote.type == "r" ? "r" : "");


        // Add three half-shorter-lasting notes
        let k = [];
        for(let i = 0; i < event.tuplet_type; i++){
            let newNote = {
                type: lastNote.type,
                symbol: newSymbol,
                duration: lastNote.duration.div(event.tuplet_type),
                in_tuplet: true,
                tuplet_type: event.tuplet_type,
                overwrite: true,
            };
            if(i+1 == event.tuplet_type){
                newNote.tuplet_end = true;
            }
            k.push(newNote);
        }
        this.notes.splice(n, 0, ...k);
        this.cursor.position = n;

    }

    this._is_supported_length = function(event){

        if(event.tuplet_type == 3){
            return true;
        }

        // Check if the note is in supported range...
        for(let i = 0; i < this.supportedLengths.length; i++)
            if (event.duration.d == this.supportedLengths[i])
                return true

        console.error("Note length not supported... ("+event.duration.d+")");
        return false;
        
    },

    this.addNote = function(event){

        let i = this.cursor.position;
        this.notes.splice(i, 0, event);

    }

    this.deletePreviousNote = function(){

        let i = this.cursor.position - 1;
        if(i >= 0 && this.notes.length > i)
            this.notes.splice(i, 1);

    }

    this.currentNote = function(){
        return this.getNote(0);
    }

    this.previousNote = function(){
        return this.getNote(-1);
    }

    this.prePreviousNote = function(){
        return this.getNote(-2);
    }

    this.getNote = function(d){
        let i = this.cursor.position + d;
        if(i >= 0 && i < this.notes.length)
            return this.notes[i];
        return null;
    }


    this.add_note = function(event) {
        
        let i = this.cursor.position;
        if(i >= 0 && i < this.notes.length && this.notes[i].overwrite && event.type != "bar"){
            this.overwrite_next(event);
            return;
        }

        this.remove_all_overwrites();

        // Check if in tuplet
        if(i >= 0 && i < this.notes.length 
            && this.notes[i].in_tuplet
            && this.notes[i - 1].in_tuplet
            && !this.notes[i - 1].tuplet_end){
            alert("CANNOT! "+i);
            return;
        }

        if(event.type != "bar" && !this._is_supported_length(event)){
            return;
        }

        // Add the note
        // Add the new note to the current position (at the cursor)
        this.notes.splice(this.cursor.position, 0, event);
        
        // Move cursor forward
        this._move_cursor_forward();
    },

    this.overwrite_next = function(event){

        //debugger;
        let i = this.cursor.position;
        let overwriteNote = this.notes[i];
        let oldDur = parseInt(overwriteNote.symbol);

        // I can only fit an equal or smaller event here
        let newDur = parseInt(event.symbol);
        if(oldDur > newDur){
            return;
            // Cannot fit bigger events here.
        }

        
        //

        if(oldDur == newDur){
            // Prepiši prvo noto v vseh primerih
            delete overwriteNote.overwrite;
            overwriteNote.symbol = event.symbol;
            overwriteNote.type = event.type;
            this.cursor.position = i + 1;
            
            // Je že vse narjeno

        }else {

            // Poglej, kolikokrat je manjša enota
            /*let times = Math.floor(newDur / oldDur) - 1;

            // Dodaj toliko - 1 pavzo
            for(let a = 0; a < times; a++){
                let copy = _.clone(overwriteNote);
                copy.symbol = parseInt(overwriteNote.symbol) + "r";
                copy.type = "r";
                copy.overwrite = true;

                // Tole je slabo. Izboljšaj
                if(overwriteNote.tuplet_end && a+1 == times){

                }else{
                    delete copy.tuplet_end;
                }
                

                this.notes.splice(i + 1 + a, 0, copy);
            }
            
            // Pavze so kopije osnovnih objektov
            // Odstrani tuplet_from in tuplet_to iz pavz
            */
            alert("To pa še ne deluje.");


        }

    

    },

    this._sum_durations = function(){

        let sum = new Fraction();
        for(var i = 0; i < this.notes.length; i++){
            sum = sum.add(this.notes[i].duration);
        }
        return sum;

    },

    this.delete_note = function() {
        
        if(this.cursor.selectionMode){
            this.selectionFunctions_delete();
            return;
        }

        // If I'm are at the beginning, I can't delete anything else
        if(this.cursor.position == 0)
        {
            // So I quit
            return;
        }

        if(this.notes.length > this.cursor.position && this.notes[this.cursor.position].tie){
            delete this.notes[this.cursor.position].tie;
        }

        if(this.notes.length > this.cursor.position + 1 && this.notes[this.cursor.position + 1].tie){
            delete this.notes[this.cursor.position + 1].tie;
        }

        if(this.notes[this.cursor.position - 1].in_tuplet){
            this.remove_tuplet();
        }

        // Move one note back - so the situation is as follows
        //
        //         |  (note to delete) (rest/note) ... (rest/note)
        // cursor--^
        this._move_cursor_backwards();
        
        // Delete this note
        this.notes.splice(this.cursor.position, 1);

        this.remove_all_overwrites();

    }

    this.remove_all_overwrites = function() {
        for(let i = 0; i < this.notes.length; i++){
            if(this.notes[i].overwrite){
                delete this.notes[i].overwrite;
            }
        }
    }

    this._move_cursor_forward = function(){

        if(this.cursor.position < this.notes.length){
            this.cursor.position ++;
        }
        else{
            this.cursor.position = this.notes.length;
        }

    }

    this._move_cursor_backwards = function(){

        if(0 >= this.cursor.position)
            this.cursor.position = 0;
        else
            this.cursor.position --;

    }


    this.check_sum_fit = function() {


        let currentDuration = new Fraction(0);
        for(let i = 0; i < this.notes.length; i++){
            
            currentDuration = currentDuration.add(this.notes[i].duration);
            let value = currentDuration.compare(1);
            if(value == 0){
                currentDuration = new Fraction(0);
                continue;
            }
            else if(value > 0){
                return false;
            }

        }

        return true;

    }



    
}

export default NoteStore