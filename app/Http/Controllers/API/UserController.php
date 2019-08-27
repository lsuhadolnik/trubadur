<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

use App\Answer;
use App\Badge;
use App\BadgeUser;
use App\Game;
use App\GameUser;

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
            'note_playback_delay' => 'integer|min:1500|max:5000',
            'clef'                => 'string|in:violin,bass',
            'avatar'              => 'image|max:16384|mimes:jpeg,bmp,png,svg+xml',
            'school_id'           => 'required|integer',
            'grade_id'            => 'required|integer',
            'badges'              => 'array',
            'rhythm_level'        => 'required|integer'
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
        return $this->prepareAndExecuteShowQuery($request, $id > 0 ? $id : $request->user()->id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
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
            'note_playback_delay' => 'integer|min:1500|max:5000',
            'clef'                => 'string|in:violin,bass',
            'avatar'              => 'image|max:16384|mimes:jpeg,bmp,png,svg+xml',
            'school_id'           => 'integer',
            'grade_id'            => 'integer',
            'badges'              => 'array',
            'rhythm_level'        => 'integer'
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
     * Potentially complete the user's badges.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request, $userId)
    {
        $completedBadgeIds = BadgeUser::where(['user_id' => $userId, 'completed' => true])->pluck('badge_id')->all();
        $badges = Badge::whereNotIn('id', $completedBadgeIds)->get(['id', 'name']);

        foreach ($badges as $badge) {
            switch ($badge->name) {
                case 'Igra brez napake': // Tole je precej čudno... Ni nujno, da ima igra 3 chapterje in 8 vprašanj na chapter
                    
                    /*$userGameIds = GameUser::where(['user_id' => $userId, 'finished' => true])
                        ->pluck('game_id')
                        ->all();
                    $count = Answer::whereIn('game_id', $userGameIds)
                        ->where(['user_id' => $userId, 'success' => true])
                        ->select(DB::raw('COUNT(*) AS total'))
                        ->groupBy('game_id')
                        ->having('total', '=', 24)
                        ->get()
                        ->count();*/

                    //if ($count > 0) {
                    if($this->hasGameWithSuccessRate($userId, 1)) 
                    {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                            ->update(['completed' => true]);
                    }

                    break;
                case 'Igra s 50% točnostjo': // Tole tudi ni nujno...
                    
                    /*$userGameIds = GameUser::where(['user_id' => $userId, 'finished' => true])
                        ->pluck('game_id')
                        ->all();
                    $count = Answer::whereIn('game_id', $userGameIds)
                        ->where(['user_id' => $userId, 'success' => true])
                        ->select('game_id', DB::raw('COUNT(*) AS total'))
                        ->groupBy('game_id')
                        ->having('total', '>=', 12)
                        ->get()
                        ->count();
                    if ($count > 0) {*/
                    if($this->hasGameWithSuccessRate($userId, 0.5))
                    {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                            ->update(['completed' => true]);
                    }
                    break;
                case 'Igra končana v 25 minutah':
                    $userGameIds = GameUser::where(['user_id' => $userId, 'finished' => true])
                        ->pluck('game_id')
                        ->all();
                    $count = Answer::whereIn('game_id', $userGameIds)
                        ->where(['user_id' => $userId])
                        ->select(DB::raw('SUM(time) AS total'))
                        ->groupBy('game_id')
                        ->having('total', '<=', 1000 * 60 * 25)
                        ->get()
                        ->count();
                    if ($count > 0) {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                            ->update(['completed' => true]);
                    }
                    break;
                case 'Dokončana igra 3 dni zapored':
                    if ($this->hasFinishedGame($userId, 2)) {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                            ->update(['completed' => true]);
                    }
                    break;
                case 'Dokončana igra 7 dni zapored':
                    if ($this->hasFinishedGame($userId, 6)) {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                            ->update(['completed' => true]);
                    }
                    break;
                case 'Dokončana igra z vsemi različnimi inštrumenti':
                    $count = GameUser::where(['user_id' => $userId, 'finished' => true])
                        ->select(DB::raw('COUNT(*)'))
                        ->groupBy('instrument')
                        ->get()
                        ->count();
                    if ($count === 5) {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                            ->update(['completed' => true]);
                    }
                    break;
                case 'Zmaga v večigralski igri':
                    $userGameIds = GameUser::where(['user_id' => $userId])
                        ->pluck('game_id')
                        ->all();
                    $gameIds = Game::whereIn('id', $userGameIds)
                        ->whereMode('multi')
                        ->pluck('id')
                        ->all();
                    foreach ($gameIds as $gameId) {
                        $userIds = GameUser::where(['game_id' => $gameId])
                            ->orderBy('points', 'desc')
                            ->pluck('user_id')
                            ->all();
                        if ($userIds[0] == $userId) {
                            BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $userId])
                                ->update(['completed' => true]);
                            break;
                        }
                    }
                    break;
            }
        }

        return response()->json([], 204);
    }

    /**
     * Check whether the user has finished a game each day in previous n days.
     *
     * @param  int  $userId
     * @param  int  $days
     * @return boolean
     */
    private function hasFinishedGame($userId, $days) {
        $success = true;
        $date = new DateTime;

        for ($i = 1; $i <= $days; $i++) {
            $date->sub(new DateInterval('P1D'));
            $count = GameUser::where(['user_id' => $userId, 'finished' => true])
                ->whereDate('created_at', '=', $date->format('Y-m-d'))
                ->get()
                ->count();
            if ($count === 0) {
                $success = false;
                break;
            }
        }

        return $success;
    }

    private function hasGameWithSuccessRate($userId, $r){

        $userGameIds = GameUser::where(['user_id' => $userId, 'finished' => true])
            ->pluck('game_id')
            ->all();

        $ratiosCount = Answer::select('game_id', 
        DB::raw('SUM(if(success = 1, 1, 0)) succeeded'), 
        DB::raw('SUM(if(success = 0,1,0)) failed'), 
        DB::raw('COUNT(*) total'))
        ->whereIn('game_id', $userGameIds)
        ->groupBy('game_id')
        ->havingRaw('succeeded / total >= ?', [$r])
        ->get()
        ->count();

        return $ratiosCount > 0;

        /* $ratios = DB::select(
            "SELECT user_id, game_id, 
            SUM(if(success = 1, 1, 0)) succeeded, 
            SUM(if(success = 0,1,0)) failed, 
            COUNT(*) total 
            from answers 
            group by game_id 
            having succeeded > 0 and failed = 0"); */

    }
}
