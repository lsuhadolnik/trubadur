<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\RhythmExerciseController;

use App\Answer;
use App\Question;
use App\Difficulty;
use App\GameUser;
use App\RhythmBar;
use App\RhythmExercise;


class GameUserController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\GameUser';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['game' => 'App\Game', 'user' => 'App\User'];

    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = [];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->prepareAndExecuteIndexQuery($request, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'game_id'    => 'required|integer',
            'user_id'    => 'required|integer',
            'instrument' => 'required|string|in:clarinet,guitar,piano,trumpet,violin',
            'points'     => 'integer',
            'finished'   => 'boolean'
        ];

        return $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gameId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $gameId, $userId)
    {
        return $this->prepareAndExecutePivotShowQuery($request, ['game_id' => $gameId, 'user_id' => $userId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gameId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gameId, $userId)
    {
        $data = [
            'instrument' => 'string|in:clarinet,guitar,piano,trumpet,violin',
            'points'     => 'integer',
            'finished'   => 'boolean'
        ];

        return $this->prepareAndExecutePivotUpdateQuery($request, $data, ['game_id' => $gameId, 'user_id' => $userId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $gameId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function destroy($gameId, $userId)
    {
        return $this->prepareAndExecutePivotDestroyQuery(['game_id' => $gameId, 'user_id' => $userId], self::MODEL);
    }

    /**
     * Finish the game for a single user. Calculate his/her points based on the answers he/she provided.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gameId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function finish(Request $request, $gameId, $userId)
    {
        $response = $this->show($request, $gameId, $userId);
        if ($response->status() != 200) {
            return $response;
        }

        $gameUser = $response->getOriginalContent();
        if ($gameUser->finished) {
            return response()->json("Game with id {$gameId} has already been finished for the user with id {$userId}.", 400);
        }

        if ($gameUser->game->mode !== 'practice') {
            $difficulty = Difficulty::find($gameUser->game->difficulty_id);
            $points = 0;

            if($gameUser->game->type == 'intervals'){
                $points = $this->gradeIntervalsGame($gameUser, $difficulty);
            }else if($gameUser->game->type == 'rhythm'){
                $points = $this->gradeRhythmGame($gameUser, $difficulty);
            }else {
                return response()->json("Game with id {$gameId} has unknown type {$gameUser->game->type}", 500);
            }

            $gameUser->points = $points;

            $gameUser->user->rating += $points;
            $gameUser->user->saveOrFail();
        }

        $gameUser->finished = true;
        $gameUser->saveOrFail();

        return response()->json([], 204);
    }

    private function gradeIntervalsGame($gameUser, $difficulty){

        $answers = Answer::where(['game_id' => $gameUser->game->id, 'user_id' => $gameUser->user->id])->with('question')->get();
        $noteCount = $difficulty->max_notes;

        $points = 0;
        foreach ($answers as $answer) {
            
            $points += 
              $this->getRangeFactor($difficulty->range) 
            * $this->getNoteCountFactor($difficulty) 
            * $this->getTimeFactor($answer->time, $answer->success) 
            * $this->getAdditionsDeletionsFactor($noteCount, $answer->nAdditions, $answer->nDeletions, $answer->success) 
            * $this->getSuccessFactor($answer->success);

        }

        return $points;

    }

    private function gradeRhythmGame($gameUser, $difficulty){

        $answers = Answer::where(['game_id' => $gameUser->game->id, 'user_id' => $gameUser->user->id])->with('question')->get();


        $points = 0;
        foreach ($answers as $answer) {
        
            $noteCount = $this->getPerfectSolverRhythmGameActionCount($answer->question);

            $p =  
              $this->getRhythmExerciseDifficultyFactor($answer->question, $answer->success)
            * $this->getRhythmTimeFactor($answer->time, $answer->success)
            * $this->getRhythmAdditionsDeletionsFactor($noteCount, $answer->nAdditions, $answer->nDeletions, $answer->success)
            * $this->getSuccessFactor($answer->success)
            * $this->getMetronomeFactor($answer)
            * $this->getNumChecksFactor($answer);

            $points += $p;

        }

        return $points;

    }

    private function getNumChecksFactor($answer) {

        $checks = 12;
        if(isset($answer->nChecks)){
            $checks = $answer->nChecks;
        }

        if($checks == 1) {
            return 2;
        } else if ($checks > 1 && $checks <= 3) {
            return 1.5;
        } else if ($checks > 3 && $checks <= 5) {
            return 1.25;
        } else if ($checks > 5 && $checks <= 8) {
            return 1.1;
        }

        return 1;


    }

    private function getMetronomeFactor($answer) {


        $metronome = true;
        if(isset($answer->metronome)) {
            $metronome = $answer->metronome;
        }

        if(!$metronome) 
        {    
            return 1.25;
        }
        else 
        {
            return 1;
        }
    }

    
    private function getSuccessFactor($success) 
    {
        if($success) {
            return 1;
        } else {
            return -0.4;
        }
    }

    private function getNoteCountFactor(Difficulty $difficulty) 
    {
        $noteCountFactors = [4 => 1, 5 => 1.2, 6 => 1.5, 7 => 1.75, 8 => 2];
        
        $noteCount = $difficulty->max_notes;
        
        return $noteCountFactors[$noteCount];
    }

    private function getPerfectSolverRhythmGameActionCount(Question $question) 
    {
        $notes = RhythmExerciseController::resolve($question->content)['notes'];

        $actions = 0;
        foreach($notes as $note){

            $diff = 1;

            // Za addition se štejejo naslednji tipi akcij
            // "n", "r", "bar", "dot", "tie", "tuplet"

            if(isset($note->dot) && $note->dot){
                $diff += 1;
            }

            if(isset($note->tie) && $note->tie){
                $diff += 1;
            }

            
            if(isset($note->tuplet_end) && $note->tuplet_end) {
                $diff += 1;
            }

            $actions += $diff;

        }

        return $actions;
    }

    /**
     * Determines the interval range factor used for calculating the points contribution of a single answer.
     *
     * @param  int  $range
     * @return float
     */
    private function getRangeFactor($range)
    {
        if ($range <= 5) {
            return 1;
        } else if ($range > 5 && $range <= 9) {
            return 1.25;
        } else {
            return 1.5;
        }
    }

    private function getRhythmExerciseDifficultyFactor($question, $success)
    {

        // Return number from 1 to 4.75
        $exercise = RhythmExercise::find($question->content);
        $difficulty = $exercise->rhythm_level;

        $points = [
            11 => 1, 12 => 1.25, 13 => 1.5, 14 => 1.75,
            21 => 2, 22 => 2.25, 23 => 2.5, 24 => 2.75,
            31 => 3, 32 => 3.25, 33 => 3.5, 34 => 3.75,
            41 => 4, 42 => 4.25, 43 => 4.5, 44 => 4.75,
        ];

        if(isset($points[$difficulty]))
            return $points[$difficulty] * 2;
        else return 1;

    }

    private function getRhythmTimeFactor($time, $success)
    {

        $time = $time / (1000 * 60);

        if (!$success) {
            return 1.5;
        }

        if ($time <= 1) {
            return 3;
        } else if ($time < 1.5) {
            return 2.5;
        } else if ($time < 2) {
            return 2;
        } else if ($time < 3) {
            return 1.5;
        } else if ($time < 5) {
            return 1.25;
        } else if ($time < 6) {
            return 1;
        } else if ($time < 7) {
            return 0.75;
        } else if ($time < 8) {
            return 0.5;
        } else if ($time < 9) {
            return 0.25;
        } else {
            return 0.1;
        }
    }

    /**
     * Determines the time factor used for calculating the points contribution of a single answer.
     *
     * @param  int  $time
     * @param  boolean  $success
     * @return float
     */
    private function getTimeFactor($time, $success)
    {
        if (!$success) {
            return 3;
        }

        if ($time <= 20000) {
            return 3;
        } else if ($time > 20000 && $time <= 25000) {
            return 2.5;
        } else if ($time > 25000 && $time <= 30000) {
            return 2;
        } else if ($time > 30000 && $time <= 40000) {
            return 1.5;
        } else if ($time > 40000 && $time <= 60000) {
            return 1;
        } else if ($time > 60000 && $time <= 80000) {
            return 0.5;
        } else {
            return 0.1;
        }
    }

    /**
     * Determines the additions/deletions factor used for calculating the points contribution of a single answer.
     *
     * @param  int  $noteCount
     * @param  int  $nAdditions
     * @param  int  $nDeletions
     * @param  boolean  $success
     * @return float
     */
    private function getRhythmAdditionsDeletionsFactor($noteCount, $nAdditions, $nDeletions, $success)
    {

        $nTotal = $nAdditions + $nDeletions - ($noteCount - 1);

        if (!$success) {

            $nTotal = ($nAdditions - $nDeletions) / ($noteCount - 1);

            if ($nTotal < 0 || $nTotal > 2) {
                return 3;
            } else if ($nTotal > 0 && $nTotal <= 1.5) {
                return 1.5;
            } else if ($nTotal > 0 && $nTotal <= 1) {
                return 1;
            } else if ($nTotal == 1) {
                return 0.5;
            }

            return 3;
        }

        

        if ($nTotal <= 2) {
            return 2;
        } else if ($nTotal > 2 && $nTotal <= 5) {
            return 1.5;
        } else if ($nTotal > 5 && $nTotal <= 9) {
            return 1;
        } else if ($nTotal > 9 && $nTotal <= 12) {
            return 0.75;
        } else if ($nTotal > 12 && $nTotal <= 16) {
            return 0.33;
        } else {
            return 0.1;
        }
    }

    private function getAdditionsDeletionsFactor($noteCount, $nAdditions, $nDeletions, $success)
    {
        if (!$success) {
            return 3;
        }

        $nTotal = $nAdditions + $nDeletions - ($noteCount - 1);

        if ($nTotal <= 5) {
            return 2;
        } else if ($nTotal > 5 && $nTotal <= 8) {
            return 1.5;
        } else if ($nTotal > 8 && $nTotal <= 10) {
            return 1;
        } else if ($nTotal > 10 && $nTotal <= 20) {
            return 0.75;
        } else {
            return 0.33;
        }
    }
}
