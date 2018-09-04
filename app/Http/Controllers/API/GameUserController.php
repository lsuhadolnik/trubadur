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
            'instrument' => 'required|string|in:clarinet,guitar,piano,trumpet,violin',
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
            $rangeFactor = $this->getRangeFactor($difficulty->range);
            $noteCountFactors = [4 => 1, 5 => 1.2, 6 => 1.5, 7 => 1.75, 8 => 2];
            $noteCount = $difficulty->max_notes;
            $noteCountFactor = $noteCountFactors[$noteCount];

            $answers = Answer::where(['game_id' => $gameId, 'user_id' => $userId])->with('question')->get();
            $successFactors = [true => 1, false => -0.75];

            $points = 0;
            foreach ($answers as $answer) {
                $points += $rangeFactor * $noteCountFactor * $this->getTimeFactor($answer->time, $answer->success) * $this->getAdditionsDeletionsFactor($noteCount, $answer->nAdditions, $answer->nDeletions, $answer->success) * $successFactors[$answer->success];
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
    private function getAdditionsDeletionsFactor($noteCount, $nAdditions, $nDeletions, $success)
    {
        if (!$success) {
            return 3;
        }

        $nTotal = $nAdditions + $nDeletions - ($noteCount - 1);

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
}
