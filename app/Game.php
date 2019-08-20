<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'difficulty_id','rhythm_difficulty_id', 'mode', 'type',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function difficulty()
    {
        return $this->belongsTo('App\Difficulty');
    }

    public function rhythm_difficulty()
    {
        return $this->belongsTo('App\RhythmDifficulty');
    }

    public function questions()
    {
        return $this->hasMany('App\Question');
    }

    public function users()
    {
        return $this->belongsToMany('App\User')->withTimestamps()->using('App\GameUser');
    }
}
