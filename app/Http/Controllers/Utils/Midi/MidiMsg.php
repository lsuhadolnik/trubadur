<?php

namespace App\Http\Controllers\Utils\Midi;

class MidiMsg {

    private static function ts($a) {
        return intval($a * 1000);
    }

    public static function On($ts, $channel, $note, $value){
        return static::ts($ts)." On ch=$channel n=$note v=$value";
    }

    public static function Off($ts, $channel, $note, $value){
        return static::ts($ts)." Off ch=$channel n=$note v=$value";
    }

    public static function ProgramChange($ts, $channel, $program){
        return static::ts($ts)." PrCh ch=$channel p=$program";
    }

    public static function Param($ts, $channel, $c, $v){
        return static::ts($ts)." Par ch=$channel c=$c v=$v";
    }

    public static function EndTrack($ts) {
        return static::ts($ts)." Meta TrkEnd";
    }

    public static function Tempo($ts) {
        return static::ts($ts)." Meta TrkEnd";
    }

}