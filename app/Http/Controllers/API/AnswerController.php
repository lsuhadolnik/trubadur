<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Question;
use App\RhythmExercise;
use App\RhythmBar;
use App\Answer;

class AnswerController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\Answer';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['game' => 'App\Game', 'user' => 'App\User', 'question' => 'App\Question'];

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
            'game_id'     => 'required|numeric',
            'user_id'     => 'required|numeric',
            'question_id' => 'required|numeric',
            'time'        => 'required|numeric|min:0|max:200000',
            'n_additions' => 'required|numeric|min:1',
            'n_deletions' => 'required|numeric|min:0',
            'n_playbacks' => 'required|numeric|min:1',
            'n_answers'   => 'required|numeric|min:1',
            'success'     => 'required|boolean'
        ];

        
        $rD = $request->all();
        $answers = Answer::where(['question_id' => $rD['question_id']])->get();
        
        if(count($answers) > 0){
            return $answers[0];
        }

        $res = $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);


        // Decrease or increase bar and exercise difficulty
        // ------------------------------------------------
        // Get status of the answer and determine difficulty delta
        $reqData = $request->all();

        // Find find the game
        $questionId = $reqData['question_id'];
        $question = Question::find($questionId);
        $game = $question->game()->first();

        if($game->type == 'rhythm'){
            $this->decreaseIncreaseDifficulty($request, $question);
        }
        
        return $res;

    }

    private function decreaseIncreaseDifficulty($request, $question) {

        $reqData = json_decode($request->getContent());
        $solved = $reqData->success;
        $diff = $solved ?  -1 :  1;

        $exerciseId = $question->content;
        $exercise = RhythmExercise::find($exerciseId);
        
        RhythmExercise::where('id', $exerciseId)->update(["difficulty" => max($exercise->difficulty + ($diff * 2), 0)]);

        // Find the bars
        $bars = $exercise->bars->all();
        foreach($bars as $bar){
            // Update the bars
            RhythmBar::where('id', $bar->id)->update(["difficulty" => max($bar->difficulty + $diff, 0)]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        return $this->prepareAndExecuteShowQuery($request, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = [
            'game_id'     => 'numeric',
            'user_id'     => 'numeric',
            'question_id' => 'numeric',
            'time'        => 'numeric|min:0|max:120000',
            'n_additions' => 'numeric|min:1',
            'n_deletions' => 'numeric|min:0',
            'n_playbacks' => 'numeric|min:1',
            'n_answers'   => 'numeric|min:1',
            'success'     => 'boolean'
        ];

        return $this->prepareAndExecuteUpdateQuery($request, $data, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->prepareAndExecuteDestroyQuery($id, self::MODEL);
    }
}
