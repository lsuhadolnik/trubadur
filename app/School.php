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

    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    public function grades()
    {
        return $this->belongsToMany('App\Grade')->withTimestamps()->using('App\GradeSchool');
    }
}
