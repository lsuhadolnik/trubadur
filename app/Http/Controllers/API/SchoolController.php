<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Level;
use App\School;

class SchoolController extends Controller
{
    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = ['grades'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = new School;
        $error = $this->setParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = School::query();
        $collection = $this->prepareAndExecuteIndexQuery($qb, [], self::PIVOT_DEPENDENCIES);

        return response()->json($collection, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $model = new School;
        $error = $this->setParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = School::query();
        $record = $this->prepareAndExecuteShowQuery($id, $qb, [], self::PIVOT_DEPENDENCIES);
        if (!$record) {
            return response()->json("School with id {$id} not found.", 404);
        }

        return response()->json($record, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!School::destroy($id)) {
            return response()->json("School with id {$id} not found.", 404);
        }

        return response()->json([], 204);
    }

    /**
     * Display the specified associated resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $schoolId
     * @param  int  $gradeId
     * @return \Illuminate\Http\Response
     */
    public function level(Request $request, $schoolId, $gradeId)
    {
        $qb = School::query();
        $school = $this->prepareAndExecuteShowQuery($schoolId, $qb);
        if (!$school) {
            return response()->json("School with id {$schoolId} not found.", 404);
        }

        $grade = $school->grades()->find($gradeId);
        if (!$grade) {
            return response()->json("Grade with id {$gradeId} not found.", 404);
        }


        $model = new Level;
        $error = $this->setParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $levelId = $grade->pivot->level_id;
        $qb = Level::query();
        $level = $this->prepareAndExecuteShowQuery($levelId, $qb);
        if (!$level) {
            return response()->json("Level with id {$levelId} not found.", 404);
        }

        return response()->json($level, 200);
    }
}
