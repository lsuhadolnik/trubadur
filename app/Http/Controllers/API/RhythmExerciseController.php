<?php

namespace App\Http\Controllers\API;

use App\RhythmExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\RhythmBar;

class RhythmExerciseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function show(RhythmExercise $rhythmExercise)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function edit(RhythmExercise $rhythmExercise)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RhythmExercise $rhythmExercise)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RhythmExercise  $rhythmExercise
     * @return \Illuminate\Http\Response
     */
    public function destroy(RhythmExercise $rhythmExercise)
    {
        //
    }

    public function generateNew(Request $request = null){

        // group rhytm_bars by barInfo and count them, HAVING COUNT(*) > 1
        $barInfos = DB::select('SELECT COUNT(*), barInfo from rhythm_bars group by barInfo having COUNT(*) > 1');
        
        if(count($barInfos) == 0) {
            throw new \Exception("Cannot generate an exercise. Insufficient bars present in database.");
        }

        shuffle($barInfos);
        $barInfoString = $barInfos[0]->barInfo;
        $barInfo = json_decode($barInfoString);


        // get IDs
        $ids = DB::select('SELECT id from rhythm_bars where barInfo = CAST(? as JSON)', [$barInfoString]);
        if(count($ids) == 0){
            throw new \Exception("Something went wrong. I received no bars from the database, although I checked before there should be at least 2 present.");
        }

        // shuffle IDs array
        shuffle($ids);

        // retrieve first 2 bars by IDs
        $bar1 = json_decode( RhythmBar::find($ids[0]->id)->content, true );
        $bar2 = json_decode( RhythmBar::find($ids[0]->id)->content, true );
        
        // split bar jsons with {type: 'bar'}
        $notes = array_merge($bar1, [["type" => "bar"]], $bar2);

        
        // return the exercise
        return json_encode(array(
            "BPM" => 100,
            "name" => "Hello",
            "bar" => $barInfo,
            "notes" => $notes
        ));

    }
}
