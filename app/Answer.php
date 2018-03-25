<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'user_id', 'question_id', 'chapter', 'number', 'time', 'n_additions', 'n_deletions', 'n_playbacks', 'success',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function gameUser()
    {
        return $this->belongsTo('App\GameUser');
    }

    public function question()
    {
        return $this->belongsTo('App\Question');
    }
}
