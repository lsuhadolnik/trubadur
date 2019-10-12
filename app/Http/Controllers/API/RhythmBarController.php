<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Input;

use App\RhythmBar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Utils\MusicXML;

class RhythmBarController extends Controller
{

    /**
     * Defines the model class.
     **/
    const MODEL = 'App\RhythmBar';

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

    public function find(Request $r) {

        $notes = $r->input('notes')['notes'];

        $ids = DB::select("SELECT id from rhythm_bars where JSON_CONTAINS(content, ?)", [json_encode($notes)]);

        

        return response()->json(array_map(function($a) {return $a->id;}, $ids));

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
    public function store(Request $request)
    {
        //

        $data = [
            'content'    => 'required|string',
            'length'     => 'required|numeric',
            'cross_bar'  => 'numeric'
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
            'content'    => 'string',
            'length'     => 'numeric',
            'cross_bar'  => 'numeric'
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

    /**
     * Import MusicXML file
     *
     * @param  \App\RhythmExerciseBar  $rhythmExerciseBar
     * @return \Illuminate\Http\Response
     */
    public function importMusicXML()
    {
        
        $file = Input::file("file");
        if(!$file) return;

        $fileContent = file_get_contents($file->getRealPath());

        $xml = new \SimpleXMLElement($fileContent);
        $takti = MusicXML::parseMeasures($xml);

        $importedIdx = [];
        foreach($takti as $t){

            $id = MusicXML::GetMeasureDatabaseIndex($t);
            if(!$id) {
                $obj = RhythmBar::create($t);
                $id = $obj->id;
                $importedIdx[] = $id;
            }
            
        }

        // RhythmBar::whereIn('id', $importedIdx)->update(["deleted_at" => null]);

        return array(
            'imported' => $importedIdx
        );

    }

}
