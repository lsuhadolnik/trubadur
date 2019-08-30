<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RhythmFeatureOccurrence extends Model
{

    public $timestamps = false;

    public $fillable = ['rhythm_feature_id', 'rhythm_level', 'feature_probability', 'bar_info_id'];

}
