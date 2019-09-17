<?php

namespace App\Http\Controllers\Utils\Generation;

use App\Http\Controllers\Utils\Generation\IModule;

class SubdivisionsModule implements IModule {

    public function __construct() {

    }

    private static function inversedSequence($inversed, $min = 0, $max) {
        if($inversed) {
            for($i = $max; $i >= $min; $i--){
                yield $i;
            }
        }else {
            for($i = $min; $i < $max; $i++){
                yield $i;
            }
        }

        return 0;
    }

    private function getNoteValue(&$note) {
        
        if(isset($note->in_tuplet) && $note->in_tuplet){
            throw new \Exception("TUPLETS NOT IMPLEMENTED!!");
        }

        $dur = 4 / $note->value;

        if(isset($note->dot) && $note->dot) {
            $dur *= 1.5;
        }

        return $dur;

    }

    public function RemLenStep(&$result, $length, &$barInfo, $barId){

        if(!isset($barInfo->subdivisions)){
            return $length;
        }
        
        // Figure out how many notes are already here
        
        $divIdx = range(0, count($barInfo->subdivisions) - 1);
        if($barId % 2 > 0) $divIdx = array_reverse($divIdx);

        $bins = array_map(function($d) use ($barInfo) { return $barInfo->subdivisions[$d]; }, $divIdx);
        $maxBins = array_map(function($d) {return (4/$d->d) * $d->n; }, $bins);
        
        $bins = array_fill(0, count($maxBins), 0);


        $idxBin = 0;
        while($length > 0){
            $diff = min($length, $maxBins[$idxBin]);
            $bins[$idxBin] += $diff;
            if($bins[$idxBin] >= $maxBins[$idxBin]) {
                $idxBin++;
            }
            $length -= $diff;
        }


        $idxBin = 0;
        $idxNote = 0;
        while($idxBin < count($bins)){
            while($bins[$idxBin] > 0 && $idxNote < count($result)){
                $bins[$idxBin] -= $this->getNoteValue($result[$idxNote++]);
            }

            if($bins[$idxBin] > 0){
                // No more notes
                break;
            }else {
                $idxBin++;
            }

        }

        if($idxBin >= count($bins)){
            return 0;
        }

        return $bins[$idxBin];

    }

    public function PreStep(&$result, &$lengths) {}
    public function PostStep(&$result, &$lengths) {}

}