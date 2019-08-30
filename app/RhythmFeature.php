<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RhythmFeature extends Model
{

    public $timestamps = false;

    public $fillable = ['name', 'max_occurrences', 'min_occurrences'];

    public function occurrences(){
        return $this->hasMany('App\RhythmFeatureOccurrence');
    }

}
