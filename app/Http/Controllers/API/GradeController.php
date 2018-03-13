<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class GradeController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\Grade';

    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = ['schools' => 'App\School'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $error = $this->setQueryParameters($request, self::MODEL);
        if ($error) {
            return response()->json($error, 400);
        }

        $collection = $this->prepareAndExecuteIndexQuery(self::MODEL, [], self::PIVOT_DEPENDENCIES);

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
        $data = [
            'grade' => 'required|numeric|unique:grades'
        ];
        $error = $this->setDataParameters($request, $data, [], self::PIVOT_DEPENDENCIES);
        if ($error) {
            return response()->json($error, 422);
        }

        $response = $this->prepareAndExecuteStoreQuery($request, self::MODEL, [], self::PIVOT_DEPENDENCIES);

        return $response;
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
        $error = $this->setQueryParameters($request, self::MODEL);
        if ($error) {
            return response()->json($error, 400);
        }

        $record = $this->prepareAndExecuteShowQuery($id, self::MODEL, [], self::PIVOT_DEPENDENCIES);
        if (!$record) {
            return response()->json("Grade with id {$id} not found.", 404);
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
        $data = [
            'grade' => ['numeric', Rule::unique('grades')->ignore($id)],
        ];
        $error = $this->setDataParameters($request, $data, [], self::PIVOT_DEPENDENCIES);
        if ($error) {
            return response()->json($error, 422);
        }

        $response = $this->prepareAndExecuteUpdateQuery($request, $id, self::MODEL, [], self::PIVOT_DEPENDENCIES);

        return $response;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return $this->prepareAndExecuteDestroyQuery($id, self::MODEL);
    }

    /**
     * Update the specified associated resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gradeId
     * @param  int  $schoolId
     * @return \Illuminate\Http\Response
     */
    public function setLevel(Request $request, $gradeId, $schoolId)
    {
        $data = [
            'level_id' => 'required|integer'
        ];
        $error = $this->setDataParameters($request, $data);
        if ($error) {
            return response()->json($error, 422);
        }

        $grade = $this->prepareAndExecuteShowQuery($gradeId, self::MODEL);
        if (!$grade) {
            return response()->json("Grade with id {$gradeId} not found.", 404);
        }

        $school = $grade->schools()->find($schoolId);
        if (!$school) {
            return response()->json("School with id {$schoolId} is not associated with grade with id {$gradeId}.", 404);
        }

        $levelId = $request->get('level_id');
        $level = $this->prepareAndExecuteShowQuery($levelId, 'App\Level');
        if (!$level) {
            return response()->json("Level with id {$levelId} not found.", 404);
        }
        $school->pivot->level_id = $levelId;
        $school->pivot->saveOrFail();

        return response()->json([], 204);
    }

    /**
     * Display the specified associated resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $gradeId
     * @param  int  $schoolId
     * @return \Illuminate\Http\Response
     */
    public function getLevel(Request $request, $gradeId, $schoolId)
    {
        $grade = $this->prepareAndExecuteShowQuery($gradeId, self::MODEL);
        if (!$grade) {
            return response()->json("Grade with id {$gradeId} not found.", 404);
        }

        $school = $grade->schools()->find($schoolId);
        if (!$school) {
            return response()->json("School with id {$schoolId} is not associated with grade with id {$gradeId}.", 404);
        }


        $error = $this->setQueryParameters($request, 'App\Level');
        if ($error) {
            return response()->json($error, 400);
        }

        $levelId = $school->pivot->level_id;
        $level = $this->prepareAndExecuteShowQuery($levelId, 'App\Level');
        if (!$level) {
            return response()->json("Level with id {$levelId} not found.", 404);
        }

        return response()->json($level, 200);
    }
}
