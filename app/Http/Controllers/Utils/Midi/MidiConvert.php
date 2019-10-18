<?php

namespace App\Http\Controllers\Utils\Midi;

use Motniemtin\Midi\Midi;
use App\Http\Controllers\Utils\Midi\MusiSONUtils;
use App\Http\Controllers\Utils\Midi\MidiMsg;

use Exception;

class MidiConvert {

    private $gain = 1;
    private $defaultSoundFont = "/usr/share/sounds/sf2/Fluid.sf2";

    public function init(){

        if(!function_exists('exec')) {
            throw new Exception("Exec can't run!");
        } 

        exec("whereis fluidsynth", $out, $return);

        $found=false;
        foreach($out as $line){
          if(substr_count($line, "/fluidsynth")){
            $found=true;break;
          }
        }

        if(!$found){
          throw new Exception("Please install fluidsynth!");
        }

    }

    public function toWav($midiFilename, $wavFilename){

        $sfFile = env("SOUNDFONT", $this->defaultSoundFont);

        $this->init();

        if(!file_exists($sfFile)){
          throw new Exception("No soundfont file found!");
        }

        if(!file_exists($midiFilename)){
          throw new Exception("No midi file found!");
        }

        if(file_exists($wavFilename)){
            unlink($wavFilename);
        }

        $gain = $this->gain;

        $cmd = "fluidsynth -F $wavFilename $sfFile -g $gain $midiFilename";
        exec($cmd);

        if(filesize($wavFilename) < 1000) {

          if(file_exists($wavFilename)) {
              unlink($wavFilename);
          }
          throw new Exception("Error converting to WAV, file is too small...");

        }

        return $wavFilename;

    }

    public function toMP3($wavFile, $mp3File) {

        $wav = $wavFile;
        $mp3 = $mp3File;

        $cmd = "ffmpeg -i $wav -ab 192k $mp3 2>&1";

        $out = [];
        $errCode = -1;

        if(file_exists($mp3)) {
            unlink($mp3);
        }

        exec($cmd, $out, $errCode);

        if(filesize($mp3File) < 1000){
          unlink($mp3File);
          throw new Exception("Error when convert WAV, file too small");
        }

        if(file_exists($wav)) {
            unlink($wav);
        }

        return $mp3;
    }

}