<?php

namespace App\Http\Controllers\Utils\Midi;

class MidiMsg {

    public function On($ts, $channel, $note, $value){
        return "$ts On ch=$channel n=$note v=$value";
    }

    public function Off($ts, $channel, $note, $value){
        return "$ts Off ch=$channel n=$note v=$value";
    }

}