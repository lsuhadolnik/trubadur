<?php

namespace App;

use App\Notifications\MailVerifyEmailToken;
use App\Notifications\MailResetPasswordToken;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'email_token', 'admin', 'rating', 'rhythm_level', 'instrument', 'note_playback_delay', 'clef', 'avatar', 'school_id', 'grade_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'email_token', 'remember_token',
    ];

    /**
     * Send an email verification email to the user.
     */
    public function sendEmailVerificationNotification($token)
    {
        $this->notify(new MailVerifyEmailToken($token));
    }

    /**
     * Send a password reset email to the user.
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new MailResetPasswordToken($token));
    }

    public function school()
    {
        return $this->belongsTo('App\School');
    }

    public function grade()
    {
        return $this->belongsTo('App\Grade');
    }

    public function logins()
    {
        return $this->hasMany('App\Login');
    }

    public function badges()
    {
        return $this->belongsToMany('App\Badge')->withTimestamps()->using('App\BadgeUser');
    }

    public function games()
    {
        return $this->belongsToMany('App\Game')->withTimestamps()->using('App\GameUser');
    }
}
