<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class LevelController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\Level';

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

        $collection = $this->prepareAndExecuteIndexQuery(self::MODEL);

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
            'level'     => 'required|string|in:easy,normal,hard',
            'range'     => 'required|numeric|min:2|max:12',
            'min_notes' => 'required|numeric|min:2|max:10',
            'max_notes' => 'required|numeric|min:4|max:10'
        ];
        $error = $this->setDataParameters($request, $data);
        if ($error) {
            return response()->json($error, 422);
        }

        $response = $this->prepareAndExecuteStoreQuery($request, self::MODEL);

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

        $record = $this->prepareAndExecuteShowQuery($id, self::MODEL);
        if (!$record) {
            return response()->json("Level with id {$id} not found.", 404);
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
            'level'     => 'string|in:easy,normal,hard',
            'range'     => 'numeric|min:2|max:12',
            'min_notes' => 'numeric|min:2|max:10',
            'max_notes' => 'numeric|min:4|max:10'
        ];
        $error = $this->setDataParameters($request, $data);
        if ($error) {
            return response()->json($error, 422);
        }

        $response = $this->prepareAndExecuteUpdateQuery($request, $id, self::MODEL);

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
