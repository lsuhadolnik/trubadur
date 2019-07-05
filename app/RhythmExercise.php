<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmExercise extends Model
{
    //

    public $fillable = ['name', 'barInfo', 'BPM', 'difficulty', 'description'];
}
