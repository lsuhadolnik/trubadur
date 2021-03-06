<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Grade extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grade',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function schools()
    {
        return $this->belongsToMany('App\School')->withTimestamps()->using('App\GradeSchool');
    }

    public function rhythmDifficulty()
    {
        return $this->hasOne('App\RhythmDifficulty');
    }
}
