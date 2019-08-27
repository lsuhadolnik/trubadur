<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmExercise extends Model
{
    //

    public $timestamps = false;

    public $fillable = ['name', 'bar_info_id', 'BPM', 'rhythm_level'];

    public function bars()
    {
        return $this->belongsToMany('App\RhythmBar', 'rhythm_exercise_bars');
    }

    public function bar_info()
    {
        return $this->hasOne('App\BarInfo');
    }
}
