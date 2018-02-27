<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'type', 'country_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function grades()
    {
        return $this->belongsToMany('App\Grade')->withPivot('level_id')->withTimestamps();
    }

    public function level($gradeId) {
        return grades()->find($gradeId)->join('level', 'level_id', 'level.id');
    }
}
