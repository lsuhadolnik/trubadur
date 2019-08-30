<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

use App\BarInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarInfoController extends Controller
{

    /**
     * Defines the model class.
     **/
    const MODEL = 'App\BarInfo';

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
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return $this->prepareAndExecuteIndexQuery($request, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    public function store(Request $request)
    {
        //

        $data = [
            'bar_info'         => 'required|string',
            'min_rhythm_level' => 'required|numeric'
        ];

        return $this->prepareAndExecuteStoreQuery($request, $data, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //
        return $this->prepareAndExecuteShowQuery($request, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = [
            'bar_info'         => 'string',
            'min_rhythm_level' => 'numeric'
        ];

        return $this->prepareAndExecuteUpdateQuery($request, $data, $id, self::MODEL, self::DEPENDENCIES, self::PIVOT_DEPENDENCIES);
    
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        return $this->prepareAndExecuteDestroyQuery($id, self::MODEL);
    }

}
