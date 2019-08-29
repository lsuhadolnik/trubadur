<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\API\RhythmExerciseController;


use App\Difficulty;
use App\RhythmDifficulty;
use App\Game;
use App\Question;

class QuestionController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\Question';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['game' => 'App\Game'];

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
            'game_id' => 'required|numeric',
            'chapter' => 'required|numeric|min:1|max:3',
            'number'  => 'required|numeric|min:1|max:8',
            'content' => 'string'
        ];

        return $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);

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
            'game_id' => 'numeric',
            'chapter' => 'numeric',
            'number'  => 'numeric',
            'content' => 'string'
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

    /**
     * Generate a new question.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {

        $game = Game::with('difficulty')->find($request->get('game_id'));

        // Retrieve already generated question
        if ($request->has('game_id') && $request->has('chapter') && $request->has('number')) {
            $question = Question::where($request->all())->first();
            if ($question) {

                if($game->type == 'rhythm'){
                    $question->content = RhythmExerciseController::resolve($question->content);
                }

                return response()->json($question, 201);
            }
        }

        $response = $this->store($request);
        if ($response->status() != 201) {
            return $response;
        }

        $question = $response->getOriginalContent();

        switch ($game->type) {
            case 'intervals':
                $question->content = $this->generateIntervalsQuestion($game->difficulty, $question);
                break;
            case 'rhythm':
                $question->content = $this->generateRhythmQuestion($game->rhythm_level, $question);
                break;
        }

        if(!$question->content){
            return response()->json(["Question" => "Not generated"], 500);
        }

        $question->saveOrFail();

        if($game->type == 'rhythm'){
            $question->content = RhythmExerciseController::resolve($question->content);
        }

        return response()->json($question, 201);
    }

    /**
     * Generate a random intervals question based on the given difficulty.
     *
     * @param  \App\Difficulty  $difficulty
     * @param  \App\Question  $question
     * @return string
     */
    private function generateIntervalsQuestion(Difficulty $difficulty, Question $question)
    {
        $difficultyRange = $difficulty->range;
        $nChapters = 3;
        $nNotes = $difficulty->max_notes - $nChapters + $question->chapter;

        $pitches = ['A#3', 'B3', 'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4', 'C5', 'C#5'];

        $sample = [];
        $pitchOccurrences = [];

        foreach ($pitches as $pitch) {
            $pitchOccurrences[$pitch] = 0;
        }

        $pitchIndex = 0;
        $topRange = 0;
        $bottomRange = 0;
        $rangeSum = 0;
        $direction = '';
        $range = 0;
        $nSemitones = 0;
        $intervalIndex = 0;

        // randomly generate first note and add it to the sample
        $pitch = $pitches[array_rand($pitches)];
        $sample[] = $pitch;

        // generate consecutive notes based on the previous note
        for ($i = 1; $i < $nNotes; $i++) {
            $pitchIndex = array_search($pitch, $pitches);

            // define possible range (steps allowed to the top / bottom)
            $topRange = count($pitches) - $pitchIndex - 1;
            $bottomRange = $pitchIndex;
            $rangeSum = $topRange + $bottomRange;

            // choose direction of the interval by using weighted random
            $direction = $this->weightedRandom(['down' => $bottomRange / $rangeSum, 'up' => $topRange / $rangeSum]);

            // potentially limit the range of the interval with the difficulties' predefined range
            $range = $direction == 'down' ? min($difficultyRange, $bottomRange) : min($difficultyRange, $topRange);

            // randomly choose the actual range
            $nSemitones = rand(0, $range);
            $intervalIndex = $direction == 'down' ? ($pitchIndex - $nSemitones) : ($pitchIndex + $nSemitones);

            // based on the range find the pitch
            $pitch = $pitches[$intervalIndex];

            // check if the pitch satisfies the defined constraints
            if ($pitchOccurrences[$pitch] === 2 || (($i === 1 || $i === $nNotes - 1) && $sample[$i - 1] === $pitch)) {
                $i--;
                continue;
            }

            // add the pitch to the sample
            $sample[] = $pitch;

            // keep track of how many times each of the pitches already occurred in the sample
            $pitchOccurrences[$pitch]++;
        }

        return implode(',', $sample);
    }
    

    /**
     * Generate a random rhythm question based on the given difficulty.
     *
     * @param  int  $difficulty
     * @param  \App\Question  $question
     * @return string
     */
    private function generateRhythmQuestion($level, Question $question){

        $r = new RhythmExerciseController();
        return $r->generateNew($level);   
        
    }

    /**
     * Generate a weighted random value based on the probabilities of each key.
     *
     * @param  array  $options
     * @return mixed
     */
    private function weightedRandom($options)
    {
        $sum = 0;
        $rand = rand(0, 1000) / 1000;
        foreach ($options as $option => $probability) {
            $sum += $probability;
            if ($rand <= $sum) {
                return $option;
            }
        }
    }
}
