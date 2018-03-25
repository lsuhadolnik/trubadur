<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class GradeSchoolController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\GradeSchool';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['grade' => 'App\Grade', 'school' => 'App\School', 'level' => 'App\Level'];

    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = [];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return $this->prepareAndExecuteIndexQuery($request, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = [
            'grade_id'  => 'required|integer',
            'school_id' => 'required|integer',
            'level_id'  => 'required|integer'
        ];

        return $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $gradeId, $schoolId)
    {
        return $this->prepareAndExecutePivotShowQuery($request, ['grade_id' => $gradeId, 'school_id' => $schoolId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $gradeId, $schoolId)
    {
        $data = [
            'level_id' => 'integer'
        ];

        return $this->prepareAndExecutePivotUpdateQuery($request, $data, ['grade_id' => $gradeId, 'school_id' => $schoolId], self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($gradeId, $schoolId)
    {
        return $this->prepareAndExecutePivotDestroyQuery(['grade_id' => $gradeId, 'school_id' => $schoolId], self::MODEL);
    }
}
