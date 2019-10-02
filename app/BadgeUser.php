<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasCompositePrimaryKey;

class BadgeUser extends Pivot
{
    use HasCompositePrimaryKey;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['badge_id', 'user_id'];

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
        'badge_id', 'user_id', 'completed', 'game_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function badge()
    {
        return $this->belongsTo('App\Badge');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
