<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmExerciseBar extends Model
{
    //
    public $timestamps = false;

    public $fillable = ['rhythm_exercise_id', 'rhythm_bar_id', 'seq'];
}
