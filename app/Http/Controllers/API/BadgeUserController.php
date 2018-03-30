<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BadgeUserController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\BadgeUser';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['badge' => 'App\Badge', 'user' => 'App\User'];

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
            'badge_id'  => 'required|integer',
            'user_id'   => 'required|integer',
            'completed' => 'boolean'
        ];

        return $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $badgeId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $badgeId, $userId)
    {
        return $this->prepareAndExecutePivotShowQuery($request, ['badge_id' => $badgeId, 'user_id' => $userId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $badgeId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $badgeId, $userId)
    {
        $data = [
            'completed' => 'boolean'
        ];

        return $this->prepareAndExecutePivotUpdateQuery($request, $data, ['badge_id' => $badgeId, 'user_id' => $userId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $badgeId
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function destroy($badgeId, $userId)
    {
        return $this->prepareAndExecutePivotDestroyQuery(['badge_id' => $badgeId, 'user_id' => $userId], self::MODEL);
    }
}
