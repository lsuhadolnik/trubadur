<?php

namespace App;

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
        'name', 'email', 'password', 'rating', 'school_id', 'grade_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

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

    public function badges()
    {
        return $this->belongsToMany('App\Badge')->withPivot('completed')->withTimestamps();
    }

    public function games()
    {
        return $this->belongsToMany('App\Game')->withPivot('points')->withTimestamps();
    }
}
