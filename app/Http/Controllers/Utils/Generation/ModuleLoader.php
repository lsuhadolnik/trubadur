<?php

namespace App\Http\Controllers\Utils\Generation;

use App\Http\Controllers\Utils\Generation\SubdivisionsModule;

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

        $this->remLengthSteps = [
            // new SubdivisionsModule()
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

    public function RunRemLengthStep(&$result, $length, &$barInfo, $barId){

        $remLen = $length;

        foreach($this->remLengthSteps as $m){
            $remLen = $m->RemLenStep($result, $remLen, $barInfo, $barId);
        }

        return $remLen;

    }

}