let _ = require('lodash');
const axios = require('axios');

// Break exercises into bars
let exercises = require('./resources/assets/js/components/games/rhythm/vajeTest.json');

let bars = [];
// {
//      id: xxx (number)   
//      content: xxx (array)
//      dbId: xxx 
// }

// Store bars separately
exercises.forEach(ex => {
    
    ex.bars = [];

    let numTypes = [];

    let currentBar = [];
    ex.notes.forEach((note, idx) => {
        if(note.type == "bar" || idx + 1 == ex.notes.length){
            
            //let barId = _.indexOf(bars, currentBar);
            let barId = _.findIndex(bars, function(item) { return _.isEqual(item.content, currentBar) });
            if(barId < 0){
                
                let newBar = {
                    id: bars.length,
                    content: currentBar,
                    barInfo: ex.bar,
                    difficulty: 100 + Math.random() * 2 * currentBar.length
                };
                bars.push(newBar);
                barId = newBar.id;

            }

            ex.bars.push(barId);
            currentBar = [];

        }else {
            currentBar.push(note);
        }
    });

});

console.log(bars.map(b => {return b.id}));
console.log(exercises.map(ex => {return {n: ex.name, bars: ex.bars}}));
console.log("First");
console.log(JSON.stringify(bars[0]));
console.log(JSON.stringify(exercises[0].bar));

// Write to DB
bars.forEach(b => {
    
    axios.post('http://localhost/api/rhythm_bar', {
        content: JSON.stringify(b.content),
        barInfo: JSON.stringify(b.barInfo),
        difficulty: b.difficulty
    })
      .then(function (response) {
            console.log("S: "+b.id+"/"+bars.length);
            //console.log(response);
      })
      .catch(function (error) {
          debugger;
        console.error("E: "+b.id+"/"+bars.length);
        console.error(error.message);

      });

});
/*



let allBarsInDB = getAllDBBars();

allBarsInDB.forEach(element => {
    
});*/

// Assign each bar an id:
//      Check if each bar exists in database
//      Store each new bar in db


// Assign each exercise an id:
//      Check if it exists by bar structure
//      