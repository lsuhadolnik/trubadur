var Fraction = require("fraction.js");

import RhythmUtilities from './rhythmUtilities';

var ExerciseGenerator = function(){

    let examples = [
        {
            bar: {
                num_beats: 4,
                base_note: 4
            }, 
            exercise: [
                    {type:"n", symbol:"4", duration: new Fraction(1,4)},
                    {type:"n", symbol:"8", duration: new Fraction(1,8)},
                    {type:"n", symbol:"8", duration: new Fraction(1,8)},
                    {type:"n", symbol:"16", duration: new Fraction(1,16)},
                    {type:"n", symbol:"16", duration: new Fraction(1,16)},
                    {type:"n", symbol:"8", duration: new Fraction(1,8)},
                    {type:"n", symbol:"4", duration: new Fraction(1,4)},
                    {type:"bar", symbol:"4", duration: new Fraction(0)},
                    
                    {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
                    {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
                    {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3, tuplet_end:true},
                    {type:"n", symbol:"16", duration: new Fraction(1,16)},
                    {type:"n", symbol:"16", duration: new Fraction(1,16)},
                    {type:"n", symbol:"8", duration: new Fraction(3,16), dot: true},
                    {type:"bar", symbol:"4", duration: new Fraction(0)},


                    {type:"n", symbol:"4", duration: new Fraction(1,4)},
                    {type:"n", symbol:"4", duration: new Fraction(1,4)},
                    {type:"n", symbol:"4", duration: new Fraction(1,4)},
                    {type:"n", symbol:"4", duration: new Fraction(1,4)},
                ],
        },
        {
            bar: {
                num_beats: 4,
                base_note: 4
            }, 
            exercise: [
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
            ],
        },
        {
            bar: {
                num_beats: 4,
                base_note: 4
            }, 
            exercise: [
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3, tuplet_end:true},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"r", symbol:"4r", duration: new Fraction(1,4)},
                {type:"r", symbol:"4r", duration: new Fraction(1,4)},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},                
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3, tuplet_end:true},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"r", symbol:"4r", duration: new Fraction(1,4)},
                {type:"r", symbol:"4r", duration: new Fraction(1,4)},
            ],
        },
        {
            bar: {
                num_beats: 4,
                base_note: 4
            }, 
            exercise: [
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet:true, tuplet_type:3},
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet:true, tuplet_type:3},
                {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet:true, tuplet_type:3, tuplet_end:true},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"4", duration: new Fraction(1,4)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
            ]
        },
        {
            bar:{
                num_beats: 3,
                base_note: 8
            },
            exercise: [
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3, tuplet_end:true},
                {type:"r", symbol:"8r", duration: new Fraction(1,8)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},


            ]
        },
        {
            BPM: 50,
            bar:{
                num_beats: 3,
                base_note: 8
            },
            exercise: [
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3, tuplet_end:true},
                {type:"r", symbol:"8r", duration: new Fraction(1,8)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},


            ]
        },
        {
            bar:{
                num_beats: 3,
                base_note: 8
            },
            exercise: [
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"8", duration: new Fraction(1,8)},
                {type:"bar", symbol:"4", duration: new Fraction(0)},

                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3},
                {type:"n", symbol:"8", duration: new Fraction(1,24), in_tuplet: true, tuplet_type: 3, tuplet_end:true},
                {type:"r", symbol:"8r", duration: new Fraction(1,8)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},
                {type:"n", symbol:"16", duration: new Fraction(1,16)},


            ]
        }
    ];

    this.currentExercise = null;
    this.currentExerciseInfo = null;

    this.currentExampleNumber = -1;
    this.generatorSequence = [];

    this.generate = function(numGen){
        
        let number = Math.floor(Math.random() * (examples.length - 1));

        this.currentExerciseInfo = examples[number];
        this.currentExercise = examples[number].exercise;

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