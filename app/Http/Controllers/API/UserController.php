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
use App\User;

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
    // const DEPENDENCIES = [];

    /**
     * Defines pivot dependencies.
     **/
    // const PIVOT_DEPENDENCIES = [];
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
            'rhythm_level'        => 'required|integer',
            'verified'            => 'integer'
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
        // return $this->prepareAndExecuteShowQuery($request, $id > 0 ? $id : $request->user()->id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
        $users = DB::select('SELECT 
            admin, avatar, clef, created_at, email, grade_id, id, 
            instrument, name, note_playback_delay, rating, rhythm_level, 
            school_id, updated_at, verified
         from users where id = ?', [$request->user()->id]);
        if(count($users) == 0) {
            throw new \Exception("No user was found for specified id");
        }

        return response()->json($users[0], 200);
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

    public function getLastGameId($user_id) {

        $coll = DB::select("SELECT game_id from game_user where user_id = ? order by game_id desc limit 1", [$user_id]);
        if(count($coll) == 0) {
            throw new \Exception("No game was found for user $game_id");
        }

        return $coll[0]->game_id;

    }

    public function badge_claimed($badge_id, $user_id, $game_id) {

        $coll = BadgeUser::where(['badge_id' => $badge_id, 'user_id' => $user_id])->get()->first();
        if($coll){
            $coll->update(['completed' => true]);
        }else {
            BadgeUser::create([
                'badge_id' => $badge_id,
                'user_id' => $user_id,
                'completed' => true, 
                'game_id' => $game_id
            ]);
        }

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

        $game_id = $this->getLastGameId($userId);
        $userRhythmLevel = $this->getUserRhythmLevel($userId);

        foreach ($badges as $badge) {
            switch ($badge->name) {
                case 'Igra brez napake': 

                    if($this->hasGameWithSuccessRate($userId, 1)) 
                    {
                        $this->badge_claimed($badge->id, $userId, $game_id);
                    }

                    break;
                case 'Igra s 50% točnostjo': 

                    if($this->hasGameWithSuccessRate($userId, 0.5))
                    {
                        $this->badge_claimed($badge->id, $userId, $game_id);
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
                        $this->badge_claimed($badge->id, $userId, $game_id);
                    }
                    break;
                case 'Dokončana igra 3 dni zapored':
                    if ($this->hasFinishedGame($userId, 2)) {
                        $this->badge_claimed($badge->id, $userId, $game_id);
                    }
                    break;
                case 'Dokončana igra 7 dni zapored':
                    if ($this->hasFinishedGame($userId, 6)) {
                        $this->badge_claimed($badge->id, $userId, $game_id);
                    }
                    break;
                case 'Dokončana igra z vsemi različnimi inštrumenti':
                    $count = GameUser::where(['user_id' => $userId, 'finished' => true])
                        ->select(DB::raw('COUNT(*)'))
                        ->groupBy('instrument')
                        ->get()
                        ->count();
                    if ($count === 5) {
                        $this->badge_claimed($badge->id, $userId, $game_id);
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
                            $this->badge_claimed($badge->id, $userId, $game_id);
                            break;
                        }
                    }
                    break;

                default:

                    $this->testForRhythmBadges($badge, $userId, $userRhythmLevel, $game_id);

                break;
            }


        }

        return response()->json([], 204);
    }

    private function testForRhythmBadges($badge, $userId, $userRhythmLevel, $game_id){

        $rhythmBadges = [
            "Znam uporabljati ritmične vaje" => [11, 10, 12],
            "Uspešni prvi koraki"            => [12, 20, 13],
            "Zdaj pa že znam"                => [13, 20, 14],
            "Napredovanje v drugi letnik"    => [14, 20, 21],
            "Dober začetek"                  => [21, 20, 22],
            "Nič me ne more ustaviti"        => [22, 25, 23],
            "Triole so mačji kašelj"         => [23, 25, 24],
            "Napredovanje v tretji letnik"   => [24, 30, 31],
            "Vajenec ritmičnega čarovnika"   => [31, 30, 32],
            "Na poti do slave"               => [32, 30, 33],
            "Sanjam šestnajstinke"           => [33, 35, 34],
            "Napredovanje v četrti letnik"   => [34, 35, 41],
            "Ritmični čarovnik"              => [41, 40, 42],
            "Kralj ritma"                    => [42, 40, 43],
            "Profesorjev asistent"           => [43, 40, 44],
            "Ritmični genij"                 => [44, 40, -1]
        ];

        if(isset($rhythmBadges[$badge->name])){

            $level = $rhythmBadges[$badge->name][0];
            $n = $rhythmBadges[$badge->name][1];
            $nextLevel = $rhythmBadges[$badge->name][2];

            if($this->hasFinishedNRhythmExercisesOfLevel($userId, $level, $n)){

                $this->badge_claimed($badge->id, $userId, $game_id);

                // Advance in rhythm level
                if( $nextLevel > 0 && $userRhythmLevel < $nextLevel  &&  $nextLevel < 41 ) {
                    
                    User::where(['id' => $userId])->update(['rhythm_level' => $nextLevel]);
                }
            }
        }
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

    private function getUserRhythmLevel($userId) {
        $coll = DB::select('SELECT rhythm_level as level from users where id = ?', [$userId]);
        return $coll[0]->level;
    }

    private function hasFinishedNRhythmExercisesOfLevel($userId, $level, $n) {

        $coll = DB::select("SELECT count(*) as k from (
            select 
                q.id as question_id
            from answers a 
                join questions q on q.id = a.question_id 
                join games g on g.id = q.game_id and g.type = 'rhythm' and g.mode != 'practice'
                join rhythm_exercises ex on ex.id = q.content and ex.rhythm_level = ?
            where a.user_id = ?  and success = 1   
            group by question_id
            ) vv;", [$level, $userId ]);

        return $coll[0]->k >= $n;
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
