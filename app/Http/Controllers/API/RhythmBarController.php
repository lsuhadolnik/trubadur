<?php

namespace App\Http\Controllers;

use App\RhythmBar;
use Illuminate\Http\Request;

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
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function show(RhythmBar $rhythmBar)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function edit(RhythmBar $rhythmBar)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, RhythmBar $rhythmBar)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\RhythmBar  $rhythmBar
     * @return \Illuminate\Http\Response
     */
    public function destroy(RhythmBar $rhythmBar)
    {
        //
    }
}
