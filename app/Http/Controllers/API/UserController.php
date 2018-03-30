<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\User';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['grade' => 'App\Grade', 'school' => 'App\School'];

    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = ['badges' => 'App\Badge', 'games' => 'App\Game'];

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
            'name'                => 'required|string|max:255|unique:users',
            'email'               => 'required|string|email|max:255|unique:users',
            'password'            => 'required|string|min:6|confirmed',
            'rating'              => 'integer',
            'instrument'          => 'string|in:clarinet,guitar,piano,trumpet,violin',
            'note_playback_delay' => 'integer|min:500|max:2500',
            'clef'                => 'string|in:violin,bass',
            'avatar'              => 'string',
            'school_id'           => 'required|integer',
            'grade_id'            => 'required|integer',
            'badges'              => 'array'
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
        return $this->prepareAndExecuteShowQuery($request, $id > 0 ?: $request->user()->id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
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
            'name'                => ['string', 'max:255', Rule::unique('users')->ignore($id)],
            'email'               => ['string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password'            => 'string|min:6|confirmed',
            'rating'              => 'integer',
            'instrument'          => 'string|in:clarinet,guitar,piano,trumpet,violin',
            'note_playback_delay' => 'integer|min:500|max:2500',
            'clef'                => 'string|in:violin,bass',
            'avatar'              => 'string',
            'school_id'           => 'integer',
            'grade_id'            => 'integer',
            'badges'              => 'array'
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
     * Update the specified associated resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gradeId
     * @param  int  $schoolId
     * @return \Illuminate\Http\Response
     */
    public function setCompleted(Request $request, $userId, $badgeId)
    {
        $data = [
            'completed' => 'required|boolean'
        ];
        $error = $this->setDataParameters($request, $data);
        if ($error) {
            return response()->json($error, 422);
        }

        $user = $this->prepareAndExecuteShowQuery($userId, self::MODEL);
        if (!$user) {
            return response()->json("User with id {$gradeId} not found.", 404);
        }

        $badgeUser = $user->badges()->find($badgeId);
        if (!$badgeUser) {
            return response()->json("Badge with id {$badgeId} is not associated with user with id {$userId}.", 404);
        }

        $badgeUser->completed = $request->get('completed');
        $badgeUser->saveOrFail();

        return response()->json([], 204);
    }

    /**
     * Display the specified associated resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gradeId
     * @param  int  $schoolId
     * @return \Illuminate\Http\Response
     */
    public function getCompleted(Request $request, $userId, $badgeId)
    {
        $user = $this->prepareAndExecuteShowQuery($userId, self::MODEL);
        if (!$user) {
            return response()->json("User with id {$gradeId} not found.", 404);
        }

        $badgeUser = $user->badges()->find($badgeId);
        if (!$badgeUser) {
            return response()->json("Badge with id {$badgeId} is not associated with user with id {$userId}.", 404);
        }

        $completed = $badgeUser->completed;

        return response()->json($completed, 200);
    }
}
