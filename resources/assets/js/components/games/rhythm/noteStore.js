//import { util } from "node-forge";

var Fraction = require("fraction.js");
let util = require('./rhythmUtilities');

var NoteStore = function(bar, cursor, render_function, info) {


    // INTERNAL NOTE FORMAT:
    // <note> 
    // {
    //     type (string): "n" or ["r", "bar"],
    //     value (number): 1  or [2,4,8,16,32,64,128,...] from supportedLengths
    // 
    //     dot (bool): false
    //     tie (bool): false,
    // 
    //     in_tuplet (bool): false,
    //     tuplet_type (bool): 3 or [2,3,4,5,6,7,8, ...] 
    // }


    // The supported note values.
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
            this.handle_add_note_button(event);
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

        debugger;

        // If in the middle of a tuplet; Delete it...
        this.clearTupletBackwards(sel.from);
        this.clearTupletForwards(sel.to);

        this.selectionFunctions_iterate((note) => {
            this.clearTupletNote(note);
        });

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

        // Move cursor to the end of the tuplet.
        this.cursor.position = sel.to + 1;

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
         *      value: 4,
         *      in_tuplet: true
         * }
         * 
         * (n:t3)
         * {
         *      type: "n",
         *      value: 4
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
        this.currentTupletInfo.type = event.value;
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
        let note = this.notes[n];

        // Don't do anything if this is the first note...
        if(n < 0) {
            return;
        }

        if(note.type == "bar"){
            return;
        }

        note.dot = !note.dot;

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
            delete note.tuplet_type;
            
            i--;
        } 
        // Odstranjuj, dokler traja ta triola ali ne trčiš ob drugo triolo
        while(i >= 0 && !this.notes[i].tuplet_end && this.notes[i].in_tuplet)


    },

    this._is_supported_length = function(event){

        // Check if the note is in supported range...
        if(this.supportedLengths.indexOf(event.value) >= 0){
            return true
        }
        
        console.error("Note value not supported... ("+event.value+")");
        return false;
        
    },

    // Adds a note to the stave
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
        return this._getNoteFromCurrentPositionDiff(0);
    }

    this.previousNote = function(){
        return this._getNoteFromCurrentPositionDiff(-1);
    }

    this.prePreviousNote = function(){
        return this._getNoteFromCurrentPositionDiff(-2);
    }

    this._getNoteFromCurrentPositionDiff = function(d){
        let i = this.cursor.position + d;
        if(i >= 0 && i < this.notes.length)
            return this.notes[i];
        return null;
    }

    this.handle_add_note_button = function(event) {

        // Check if in tuplet
        if(this.inTheMiddleOfATuplet()){
            this.remove_tuplet();
            // And continue adding this event
        }

        if(event.type != "bar" && !this._is_supported_length(event)){
            return;
        }

        // Add the note
        // Add the new note to the current position (at the cursor)
        this.addNote(event);
        
        // Move cursor forward
        this._move_cursor_forward();
    

    },

    this._remove_tie_at_cursor = function() {

        let pos = this.cursor.position;

        if(this.notes.length > pos && this.notes[pos].tie){
            delete this.notes[pos].tie;
        }

        if(this.notes.length > pos + 1 && this.notes[pos + 1].tie){
            delete this.notes[pos + 1].tie;
        }

    },

    this.delete_note = function() {
        
        if(this.cursor.selectionMode){
            this.selectionFunctions_delete();
            return;
        }

        // If I'm are at the beginning, I can't delete anything else
        if(this.cursor.position == 0) { return; }

        // If there ate ties, remove them.
        this._remove_tie_at_cursor()

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

    }

    this._move_cursor_forward = function(){

        if(this.cursor.position < this.notes.length){
            this.cursor.position ++;
        }
        else{
            this.cursor.position = this.notes.length;
        }

        this.cursor.cursor_moved();

    }

    this._move_cursor_backwards = function(){

        if(0 >= this.cursor.position)
            this.cursor.position = 0;
        else
            this.cursor.position --;

        this.cursor.cursor_moved();

    }

    
}

export default NoteStore