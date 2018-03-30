<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GeneratorController extends Controller
{
    /**
     * Show the intervals generator.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('extra.generator');
    }
}
