<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;

use App\Level;

class LevelController extends Controller
{
    /**
     * Defines the model class.
     **/
    const MODEL = 'App\Level';

    /**
     * Defines dependencies.
     **/
    const DEPENDENCIES = [];

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
            'level'      => 'required|numeric',
            'min_rating' => 'required|numeric',
            'max_rating' => 'required|numeric'
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
    public function show(Request $request, $id)
    {
        return $this->prepareAndExecuteShowQuery($request, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
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
            'level'      => ['integer', Rule::unique('levels')->ignore($id)],
            'min_rating' => 'numeric',
            'max_rating' => 'numeric'
        ];

        return $this->prepareAndExecuteUpdateQuery($request, $data, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
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
     * Find the level base on the given rating.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function find(Request $request)
    {
        if (!$request->has('rating')) {
            return response()->json("Set 'rating' query parameter.", 400);
        }

        $rating = $request->get('rating');
        $level = Level::where([['min_rating', '<=', $rating], ['max_rating', '>', $rating]])->first();
        if (!$level) {
            response()->json("Level not found for the given 'rating'.", 404);
        }

        return response()->json($level, 200);
    }
}
