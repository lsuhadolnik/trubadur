<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmExerciseFeedback extends Model
{
    //
    public $table = "rhythm_exercise_feedbacks";
    public $fillable = ['content', 'rhythm_exercise_id'];


    public function rhythm_exercise() {
        return $this->belongsTo('App\RhythmExercise');
    }

}
