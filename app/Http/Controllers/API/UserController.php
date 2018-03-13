<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\User';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = ['school' => 'App\School', 'grade' => 'App\Grade'];

    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = ['badges' => 'App\Badge'];

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
            'name'      => 'required|string|max:255|unique:users',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6|confirmed',
            'rating'    => 'integer',
            'school_id' => 'required|integer',
            'grade_id'  => 'required|integer',
            'badges'    => 'array'
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
            return response()->json("User with id {$id} not found.", 404);
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
            'name'      => ['string', 'max:255', Rule::unique('users')->ignore($id)],
            'email'     => ['string', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'password'  => 'required|string|min:6|confirmed',
            'rating'    => 'integer',
            'school_id' => 'required|integer',
            'grade_id'  => 'required|integer',
            'badges'    => 'array'
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
}
