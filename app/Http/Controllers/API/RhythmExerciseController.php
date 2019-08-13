<?php

namespace App\Http\Controllers\API;

use App\RhythmExercise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

use App\RhythmBar;
use App\RhythmExerciseBar;

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


    public static function resolve(int $id){

        $ex = RhythmExercise::find($id);

        $bars = $ex->bars->all();

        // split bar jsons with {type: 'bar'}
        $notes = json_decode($bars[0]->content, true);
        for($i = 1; $i < count($bars); $i++){
            $notes = array_merge($notes, [["type" => "bar"]], json_decode($bars[$i]->content, true));
        }

        
        // return the exercise
        return array(
            "BPM" => $ex->BPM,
            "name" => $ex->name,
            "bar" => json_decode($ex->barInfo),
            "notes" => $notes
        );

    }

    private function FindExistingExercise($id1, $id2) {

        // 1: $id1, 2: $id2; Exercise
        $res = DB::select("SELECT
        k.rhythm_exercise_id as exid
        FROM (
            SELECT
            v.rhythm_exercise_id,
            IF(v.rhythm_bar_id = ? && v.seq = 1 || v.rhythm_bar_id = ? && v.seq = 2, 1, 0) as c
            FROM rhythm_exercise_bars v
            where v.rhythm_exercise_id in 
            (
                SELECT rhythm_exercise_id 
                FROM rhythm_exercise_bars 
                where 
                    rhythm_bar_id = ? or 
                    rhythm_bar_id = ? 
                group by rhythm_exercise_id 
                having count(*) = 2
            )
        ) k
        group by k.rhythm_exercise_id
        HAVING SUM(c) = 2;", 
        [$id1, $id2, $id1, $id2]);

        if(count($res) == 0){
            return null;
        }

        return $res[0]->exid;

    }

    private function generateShufled() {

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

        // Does such exercie already exist?
        $existingExerciseId = $this->FindExistingExercise($ids[0]->id, $ids[1]->id);
        if($existingExerciseId != null){
            return $existingExerciseId;
        }

        // retrieve first 2 bars by IDs
        $bar1 = RhythmBar::find($ids[0]->id);
        $bar2 = RhythmBar::find($ids[1]->id);

        $difficulty = $bar1->difficulty + $bar2->difficulty;
        if(!$difficulty){
            $difficulty = 100;
        }
        
        $ex = RhythmExercise::create([
            "name" => "Random " . time(),
            "barInfo" => $barInfoString,
            "BPM" => 60,
            "difficulty" => $bar1->difficulty + $bar2->difficulty,
            //"description" => ""
        ]);

        RhythmExerciseBar::create([
            'rhythm_exercise_id' => $ex->id,
            'rhythm_bar_id' => $bar1->id,
            'seq' => 1
        ]);

        RhythmExerciseBar::create([
            'rhythm_exercise_id' => $ex->id,
            'rhythm_bar_id' => $bar2->id,
            'seq' => 2
        ]);

        return $ex->id;

    }

    public function generateNew(Request $request = null){

        return $this->generateShufled();

    }
}
