<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmDifficulty extends Model
{
    //

    protected $table = 'rhythm_difficulties';

    protected $fillable = ['grade_id', 'min_difficulty', 'max_difficulty'];

    public function grade() {
        return $this->belongsTo('App\Grade');
    }
}
