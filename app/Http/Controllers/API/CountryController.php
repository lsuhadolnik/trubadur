<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

class CountryController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\Country';

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
            'name' => 'required|string|unique:countries'
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
            return response()->json("Country with id {$id} not found.", 404);
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
            'name' => ['string', Rule::unique('countries')->ignore($id)]
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
}
