<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use DateInterval;
use DateTime;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

use App\Badge;
use App\BadgeUser;
use App\Login;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        $login = new Login;

        $login->user()->associate($user);

        $login->saveOrFail();

        $completedBadgeIds = BadgeUser::where(['user_id' => $user->id, 'completed' => true])->pluck('badge_id')->all();
        $badges = Badge::whereNotIn('id', $completedBadgeIds)->get(['id', 'name']);

        foreach ($badges as $badge) {
            switch ($badge->name) {
                case 'Prijava 3 dni zapored':
                    if ($this->hasLoggedIn($user->id, 2)) {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $user->id])
                            ->update(['completed' => true]);
                    }
                    break;
                case 'Prijava 7 dni zapored':
                    if ($this->hasLoggedIn($user->id, 6)) {
                        BadgeUser::where(['badge_id' => $badge->id, 'user_id' => $user->id])
                            ->update(['completed' => true]);
                    }
                    break;
            }
        }
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return array_merge($request->only($this->username(), 'password'), ['verified' => true]);
    }

    /**
     * Check whether the user has logged in each day in previous n days.
     *
     * @param  int  $userId
     * @param  int  $days
     * @return boolean
     */
    private function hasLoggedIn($userId, $days) {
        $success = true;
        $date = new DateTime;

        for ($i = 1; $i <= $days; $i++) {
            $date->sub(new DateInterval('P1D'));
            $count = Login::where(['user_id' => $userId])
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
}
