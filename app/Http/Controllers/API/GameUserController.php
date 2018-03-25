<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
            'game_id' => 'required|integer',
            'user_id' => 'required|integer',
            'points'  => 'integer'
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
    public function show(Request $request, $gameId, $userId)
    {
        return $this->prepareAndExecutePivotShowQuery($request, ['game_id' => $gameId, 'user_id' => $userId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gameId, $userId)
    {
        $data = [
            'points' => 'integer'
        ];

        return $this->prepareAndExecutePivotUpdateQuery($request, $data, ['game_id' => $gameId, 'user_id' => $userId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($gameId, $userId)
    {
        return $this->prepareAndExecutePivotDestroyQuery(['game_id' => $gameId, 'user_id' => $userId], self::MODEL);
    }
}
