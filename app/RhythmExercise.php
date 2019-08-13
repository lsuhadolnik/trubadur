<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmExercise extends Model
{
    //

    public $timestamps = false;

    public $fillable = ['name', 'barInfo', 'BPM', 'difficulty', 'description'];

    public function bars()
    {
        return $this->belongsToMany('App\RhythmBar', 'rhythm_exercise_bars');
    }
}
