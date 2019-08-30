<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RhythmBar extends Model
{

    use SoftDeletes;

    public $timestamps = false;

    public $fillable = ['content', 'length', 'cross_bar'];

    public function newQuery($excludeDeleted = true) {
        return parent::newQuery($excludeDeleted)
            ->where('id', '>', 1);
    }

    public function occurrences(){
        return $this->hasMany('App\RhythmBarOccurrence');
    }
}
