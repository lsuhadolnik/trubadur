<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use App\Game;
use App\Level;
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
            'number'  => 'required|numeric|min:1|max:8'
        ];

        if ($request->has('game_id') && $request->has('chapter') && $request->has('number')) {
            $question = Question::where($request->all())->first();
            if ($question) {
                return response()->json($question, 200);
            }
        }

        $response = $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
        if ($response->status() != 201) {
            return $response;
        }

        $question = $response->getOriginalContent();
        $game = Game::with('level')->find($request->get('game_id'));

        switch ($game->type) {
            case 'intervals':
                $question->content = $this->generateIntervalsQuestion($game->level, $question);
                break;
            case 'rhythm':
                break;
        }
        $question->saveOrFail();

        return response()->json($question, 201);
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
            'number'  => 'numeric'
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
     * Generates a random intervals question based on the given level.
     *
     * @param  \App\Level  $level
     * @param  \App\Question  $question
     * @return string
     */
    private function generateIntervalsQuestion(Level $level, Question $question)
    {
        $levelRange = $level->range;
        switch ($level->level) {
            case 'easy':
            case 'normal':
                $nNotes = $level->min_notes + $question->chapter - 1;
                break;
            case 'hard':
                $nNotes = $level->min_notes + 2 * ($question->chapter - 1);
                break;
        }

        $pitches = ['A#3', 'B3', 'C4', 'C#4', 'D4', 'D#4', 'E4', 'F4', 'F#4', 'G4', 'G#4', 'A4', 'A#4', 'B4', 'C5', 'C#5'];

        $sample = [];
        $pitchIndex = 0;
        $topRange = 0;
        $bottomRange = 0;
        $range = 0;
        $direction = '';
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

            // randomly choose direction of the interval
            $direction = rand(0, 1) ? 'down' : 'up';

            // potentially limit the range of the interval with the level's predefined range
            $range = $direction == 'down' ? min($levelRange, $bottomRange) : min($levelRange, $topRange);

            // randomly choose the actual range
            $nSemitones = rand(0, $range);
            $intervalIndex = $direction == 'down' ? ($pitchIndex - $nSemitones) : ($pitchIndex + $nSemitones);

            // based on the range find the pitch and add it to the sample
            $pitch = $pitches[$intervalIndex];
            $sample[] = $pitch;
        }

        return implode(',', $sample);
    }
}
