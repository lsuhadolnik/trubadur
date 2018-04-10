<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Answer;
use App\Difficulty;
use App\GameUser;

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
            'game_id'  => 'required|integer',
            'user_id'  => 'required|integer',
            'points'   => 'integer',
            'finished' => 'boolean'
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
            'points'   => 'integer',
            'finished' => 'boolean'
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
     * Finish the game for a single user. Calculate his points based on the answers he provided.
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
        // if ($gameUser->finished) {
        //     return response()->json("Game with id {$gameId} has already been finished for the user with id {$userId}.", 400);
        // }

        if ($gameUser->game->mode !== 'practice') {
            $difficulty = Difficulty::find($gameUser->game->difficulty_id);
            $rangeFactor = $this->getRangeFactor($difficulty->range);
            $noteCountFactors = [4 => 1, 5 => 1.2, 6 => 1.5, 7 => 1.75, 8 => 2];
            $noteCountFactor = $noteCountFactors[$difficulty->max_notes];

            $answers = Answer::where(['game_id' => $gameId, 'user_id' => $userId])->with('question')->get();
            $successFactors = [true => 1.1, false => -0.75];

            $points = 0;
            foreach ($answers as $answer) {
                $points += $rangeFactor * $noteCountFactor * $this->getTimeFactor($answer->time, $answer->success) * $successFactors[$answer->success];
            }

            $gameUser->points = $points;

            $gameUser->user->rating += $points;
            $gameUser->user->saveOrFail();
        }

        $gameUser->finished = true;
        $gameUser->saveOrFail();

        return response()->json([], 204);
    }

    /**
     * Determines the interval range factor used for calculating the points contribution of a single answer.
     *
     * @param  int  $range
     * @return int
     */
    private function getRangeFactor($range)
    {
        if ($range <= 5) {
            return 1;
        } else if ($range > 5 && $range <= 9) {
            return 1.75;
        } else {
            return 2.5;
        }
    }

    /**
     * Determines the time factor used for calculating the points contribution of a single answer.
     *
     * @param  int  $time
     * @param  boolean  $success
     * @return int
     */
    private function getTimeFactor($time, $success)
    {
        if (!$success) {
            return 1;
        }

        if ($time <= 30000) {
            return 3;
        } else if ($time > 30000 && $time <= 60000) {
            return 2;
        } else if ($time > 60000 && $time <= 90000) {
            return 1;
        } else {
            return 0.75;
        }
    }
}
