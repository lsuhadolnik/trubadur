var Fraction = require("fraction.js");

let RhythmUtilities = require('./rhythmUtilities');

var ExerciseGenerator = function(){

    let examples = require("./vaje.json");

    this.currentExercise = null;
    this.currentExerciseInfo = null;

    this.currentExampleNumber = -1;
    this.generatorSequence = [];


    this.generate = function(numGen){
        
        let number = Math.floor(Math.random() * (examples.length - 1));

        this.currentExerciseInfo = examples[number];
        this.currentExercise = examples[number].notes;

        return examples[number];
    }

    this.check = function(value){

        var soundsLikeFunc = RhythmUtilities.generate_playback_durations;

        // Return string fractions
        let ex = soundsLikeFunc(this.currentExercise, true);
        let us = soundsLikeFunc(value, true);

        return _.isEqual(ex, us);

    }

    this.get_bar_count = function(){

        return RhythmUtilities.get_bar_count(this.currentExercise);

    }

}

export default ExerciseGenerator;