<?php

namespace App\Http\Controllers\API;

use App\RhythmExerciseBar;
use App\RhythmBar;
use Illuminate\Http\Request;
use App\Http\Controllers\Utils\MusicXML;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;

class RhythmExerciseBarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
     * @param  \App\RhythmExerciseBar  $rhythmExerciseBar
     * @return \Illuminate\Http\Response
     */
    public function show(RhythmExerciseBar $rhythmExerciseBar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RhythmExerciseBar  $rhythmExerciseBar
     * @return \Illuminate\Http\Response
     */
    public function edit(RhythmExerciseBar $rhythmExerciseBar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RhythmExerciseBar  $rhythmExerciseBar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RhythmExerciseBar $rhythmExerciseBar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RhythmExerciseBar  $rhythmExerciseBar
     * @return \Illuminate\Http\Response
     */
    public function destroy(RhythmExerciseBar $rhythmExerciseBar)
    {
        //
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

        return array(
            'imported' => $importedIdx
        );

    }

}
