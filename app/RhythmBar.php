<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RhythmBar extends Model
{
    public $timestamps = false;

    public $fillable = ['content', 'barInfo', 'difficulty'];
}
