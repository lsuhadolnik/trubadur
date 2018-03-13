<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class SchoolController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\School';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['country' => 'App\Country'];

    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = ['grades' => 'App\Grade'];

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

        $collection = $this->prepareAndExecuteIndexQuery(self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);

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
            'name'       => 'required|string|unique:schools',
            'type'       => 'required|string|in:primary,high,university',
            'country_id' => 'required|integer',
            'grades'     => 'array'
        ];
        $error = $this->setDataParameters($request, $data, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
        if ($error) {
            return response()->json($error, 422);
        }

        $response = $this->prepareAndExecuteStoreQuery($request, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);

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

        $record = $this->prepareAndExecuteShowQuery($id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
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
        $data = [
            'name'       => ['string', Rule::unique('schools')->ignore($id)],
            'type'       => 'string|in:primary,high,university',
            'country_id' => 'integer',
            'grades'     => 'array'
        ];
        $error = $this->setDataParameters($request, $data, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
        if ($error) {
            return response()->json($error, 422);
        }

        $response = $this->prepareAndExecuteUpdateQuery($request, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);

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
     * @param  int  $schoolId
     * @param  int  $gradeId
     * @return \Illuminate\Http\Response
     */
    public function setLevel(Request $request, $schoolId, $gradeId)
    {
        $data = [
            'level_id' => 'required|integer'
        ];
        $error = $this->setDataParameters($request, $data);
        if ($error) {
            return response()->json($error, 422);
        }

        $school = $this->prepareAndExecuteShowQuery($schoolId, self::MODEL);
        if (!$school) {
            return response()->json("School with id {$schoolId} not found.", 404);
        }

        $grade = $school->grades()->find($gradeId);
        if (!$grade) {
            return response()->json("Grade with id {$gradeId} is not associated with school with id {$schoolId}.", 404);
        }

        $levelId = $request->get('level_id');
        $level = $this->prepareAndExecuteShowQuery($levelId, 'App\Level');
        if (!$level) {
            return response()->json("Level with id {$levelId} not found.", 404);
        }
        $grade->pivot->level_id = $levelId;
        $grade->pivot->saveOrFail();

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
    public function getLevel(Request $request, $schoolId, $gradeId)
    {
        $school = $this->prepareAndExecuteShowQuery($schoolId, self::MODEL);
        if (!$school) {
            return response()->json("School with id {$schoolId} not found.", 404);
        }

        $grade = $school->grades()->find($gradeId);
        if (!$grade) {
            return response()->json("Grade with id {$gradeId} is not associated with school with id {$schoolId}.", 404);
        }


        $error = $this->setQueryParameters($request, 'App\Level');
        if ($error) {
            return response()->json($error, 400);
        }

        $levelId = $grade->pivot->level_id;
        $level = $this->prepareAndExecuteShowQuery($levelId, 'App\Level');
        if (!$level) {
            return response()->json("Level with id {$levelId} not found.", 404);
        }

        return response()->json($level, 200);
    }
}
