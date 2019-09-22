<?php

namespace App\Http\Controllers\API;

use Telegram\Bot\Api;

use App\RhythmExerciseFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

use App\Question;
use App\RhythmExercise\Feedback;

class RhythmExerciseFeedbackController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\RhythmExerciseFeedback';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['rhythm_exercise' => 'App\RhythmExercise'];

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

    private function sendTelegram($exId, $content) {
        if(env('TELEGRAM_API_KEY') && env('TELEGRAM_CHAT_ID')){
            try{

                $text = "($exId) ";
                $text .= $content;


                $telegram = new Api(env('TELEGRAM_API_KEY'));
                $response = $telegram->sendMessage([
                    'chat_id' => env('TELEGRAM_CHAT_ID'), 
                    'text' => $text
                ]);

            }
            catch(\Exception $ex){

            }
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $req = (object) $request->all();
        if(!isset($req->content)){
            throw new \Exception("Content is required");
        }

        
        $exId = -1;

        if(isset($req->rhythm_exercise_id)){
            $exId = $req->rhythm_exercise_id;

        }else if(isset($req->question_id)){
            $q = Question::findOrFail($req->question_id);
            $exId = $q->content;

        }

        $this->sendTelegram($exId, $req->content);

        return RhythmExerciseFeedback::create([
            "rhythm_exercise_id" => $exId,
            "content" => $req->content
        ]);
        
        // $data = [
        //     'rhythm_exercise_id' => 'integer',
        //     'content' => 'required|string'
        // ];
        // 
        // return $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);

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
            'rhythm_exercise_id' => 'required|numeric',
            'content' => 'required|string'
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
