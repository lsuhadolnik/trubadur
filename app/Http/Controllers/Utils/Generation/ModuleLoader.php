<?php

namespace App\Http\Controllers\Utils;

class ModuleLoader {

    /**
     * Contains module instances
     */
    private $preSteps = [];
    private $postSteps = [];

    function __construct() {
        $this->preSteps = [
            
        ];

        $this->postSteps = [

        ];
    }

    public function RunPreSteps(&$result, &$lengths){

        foreach($this->preSteps as $m){
            $m->PreStep($result, $lengths);
        }

    }

    public function RunPostSteps(&$result, &$lengths) {
        
        foreach($this->postSteps as $m){
            $m->PostStep($result, $lengths);
        }

    }

}