<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Traits\HasCompositePrimaryKey;

class GradeSchool extends Pivot
{
    use HasCompositePrimaryKey;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = ['grade_id', 'school_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'grade_id', 'school_id', 'difficulty_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    public function grade()
    {
        return $this->belongsTo('App\Grade');
    }

    public function school()
    {
        return $this->belongsTo('App\School');
    }

    public function difficulty()
    {
        return $this->belongsTo('App\Difficulty');
    }
}
