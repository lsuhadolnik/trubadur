<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RhythmBarOccurrence extends Model
{

    public $timestamps = false;

    public $fillable = ['rhythm_bar_id', 'rhythm_feature_id', 'bar_probability'];

}
