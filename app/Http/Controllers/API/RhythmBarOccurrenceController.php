<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

use App\RhythmBar;
use App\RhythmBarOccurrence;

class RhythmBarOccurrenceController extends Controller
{

    /**
     * Defines the model class.
     **/
    const MODEL = 'App\RhythmBarOccurrence';

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
        return RhythmBar::find($id)->occurrences;
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
            'rhythm_feature_id'    => 'required|numeric',
            'bar_probability'    => 'required|numeric',
        ];

        $validator = Validator::make($request->all(), $data);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }
        $kk = $request->all();
        $kk['rhythm_bar_id'] = $id;

        return RhythmBarOccurrence::create($kk);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id, $fid)
    {
        //
        return RhythmBarOccurrence::where(['rhythm_bar_id'=>$id, 'rhythm_feature_id'=>$fid])->get()->first();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $fid)
    {
        $data = [
            'bar_probability' => 'numeric',
        ];
        $validator = Validator::make($request->all(), $data);
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        $newProb = $request->all()['bar_probability'];

        return RhythmBarOccurrence::where(['rhythm_bar_id'=>$id, 'rhythm_feature_id'=>$fid])->update(['bar_probability' => $newProb]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $fid)
    {
        return RhythmBarOccurrence::where(['rhythm_bar_id'=>$id, 'rhythm_feature_id'=>$fid])->delete();
    }

}
