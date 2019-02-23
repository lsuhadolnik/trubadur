var Fraction = require("fraction.js");

var ExerciseGenerator = function(soundsLikeFunc){

    let examples = [
        [
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            
            {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
            {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3},
            {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet: true, tuplet_type: 3, tuplet_from:7, tuplet_to:10},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"8", duration: new Fraction(3,16), dot: true},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
        ],
        [
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},

            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            
        ],
        [
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},
            {type:"n", symbol:"16", duration: new Fraction(1,16)},

            {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet:true, tuplet_type:3},
            {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet:true, tuplet_type:3},
            {type:"n", symbol:"4", duration: new Fraction(1,12), in_tuplet:true, tuplet_type:3, tuplet_from:10, tuplet_to: 13},
            
            {type:"n", symbol:"4", duration: new Fraction(1,4)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
            {type:"n", symbol:"8", duration: new Fraction(1,8)},
        ]
    ];

    this.currentExercise = null;

    this.generate = function(){
        let number = Math.round(Math.random() * (examples.length - 1));
        
        this.currentExercise = examples[number];

        return examples[number];
    }

    this.generate();

    this.check = function(value){

        // Return string fractions
        let ex = soundsLikeFunc(this.currentExercise, true);
        let us = soundsLikeFunc(value, true);

        if(_.isEqual(ex, us)){
            alert("PRAVILNO!");
        }else{
            alert("Ni še čisto v redu.");
        }

    }

}

export default ExerciseGenerator;