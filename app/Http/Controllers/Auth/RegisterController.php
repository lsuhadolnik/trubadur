<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

use App\Badge;
use App\Grade;
use App\School;
use App\User;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
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
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name'     => 'required|string|max:255|unique:users',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        return redirect()->back()->with('status', 'Sporočilo za potrditev email naslova je bilo poslano!');
    }

    /**
     * Create a new user instance after a valid registration and send an email to confirm the registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $user = new User;

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = bcrypt($data['password']);
        $user->email_token = Crypt::encrypt([
            'name' => $data['name'],
            'email' => $data['email']
        ]);

        $school = School::whereName('Konservatorij za glasbo in balet Ljubljana')->first();
        $user->school()->associate($school);

        $grade = Grade::whereGrade(1)->first();
        $user->grade()->associate($grade);

        $user->saveOrFail();

        $user->sendEmailVerificationNotification($user->email_token);

        return $user;
    }

    /**
     * Process the email verification request and verify the user.
     *
     * @param  string  $token
     * @return \Illuminate\Http\Response
     */
    protected function verify($token = null)
    {
        if (!$token) {
            return redirect()->route('login')->withErrors(['email_token' => 'Potrditev email naslova ni uspela! Žeton ni prisoten.']);
        }

        try {
            $data = Crypt::decrypt($token);
        } catch (DecryptException $e) {
            return redirect()->route('login')->withErrors(['email_token' => 'Potrditev email naslova ni uspela! Žeton je okvarjen.']);
        }

        $user = User::where(array_merge($data, ['email_token' => $token]))->first();
        if (!$user) {
            return redirect()->route('login')->withErrors(['email_token' => 'Potrditev email naslova ni uspela! Žeton je neveljaven.']);
        }

        $user->verified = true;
        $user->email_token = null;

        $badgeIds = Badge::pluck('id')->all();
        $user->badges()->sync($badgeIds);

        $user->save();

        return redirect()->route('login')->with('status', 'Potrditev email naslova je uspela! Sedaj se lahko prijavite.');
    }
}
