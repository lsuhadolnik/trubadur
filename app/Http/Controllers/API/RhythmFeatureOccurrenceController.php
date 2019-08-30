<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\RhythmFeatureOccurrence;
use App\RhythmFeature;

class RhythmFeatureOccurrenceController extends Controller
{

    /**
     * Defines the model class.
     **/
    const MODEL = 'App\RhythmFeatureOccurrence';

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
    public function index(Request $request, $id)
    {
        return RhythmFeature::find($id)->occurrences;
    }

    /**
     * Store a newly created resource in storage.
     * Accepts one or multiple rhythm bars - JSON array
     * in format:
     * [
     *  {
     *      content: [...notes...],
     *      barInfo: {},
     *      difficulty: {}
     *  }
     * ]
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        //

        $data = [
            'bar_info_id'    => 'required|numeric',
            'feature_probability'    => 'required|numeric',
            'rhythm_level'    => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $data);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $kk = $request->all();
        $kk['rhythm_feature_id'] = $id;

        return RhythmFeatureOccurrence::create($kk);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $fid, $level, $bar)
    {
        //
        return RhythmFeatureOccurrence::where(['rhythm_feature_id'=>$fid, 'rhythm_level'=>$level, 'bar_info_id'=>$bar])->get()->all();
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
            'bar_info_id'    => 'required|numeric',
            'feature_probability'    => 'numeric',
            'rhythm_level'    => 'required|numeric',
        ];
        $validator = Validator::make($request->all(), $data);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $newProb = $request->all()['feature_probability'];
        $level = $request->all()['rhythm_level'];
        $barInfoId = $request->all()['bar_info_id'];

        return RhythmFeatureOccurrence::where([
            'rhythm_feature_id'=>$id, 
            'rhythm_level'=>$level,
            'bar_info_id' => $barInfoId])->update(['feature_probability' => $newProb]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($fid, $level, $bar)
    {
        return RhythmFeatureOccurrence::where([
            'rhythm_feature_id'=>$fid, 
            'bar_info_id'=>$bar, 
            'rhythm_level'=>$level
        ])->delete();
    }

}
