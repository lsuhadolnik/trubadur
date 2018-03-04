<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Country;

class CountryController extends Controller
{
    /**
     * Defines pivot dependencies.
     **/
    const PIVOT_DEPENDENCIES = ['schools'];

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $model = new Country;
        $error = $this->setParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = Country::query();
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
        $model = new Country;
        $error = $this->setParameters($request, $model);
        if ($error) {
            return response()->json($error, 400);
        }

        $qb = Country::query();
        $record = $this->prepareAndExecuteShowQuery($id, $qb, [], self::PIVOT_DEPENDENCIES);
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
        if (!Country::destroy($id)) {
            return response()->json("Country with id {$id} not found.", 404);
        }

        return response()->json([], 204);
    }
}
