<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasCompositePrimaryKey;

class GameUser extends Pivot
{
    use HasCompositePrimaryKey;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['game_id', 'user_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'game_id', 'user_id', 'instrument', 'points', 'finished',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function game()
    {
        return $this->belongsTo('App\Game');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function answers()
    {
        return $this->hasMany('App\Answer');
    }
}
