<?php

namespace App\Http\Controllers\Utils\Midi;

use Motniemtin\Midi\Midi;
use App\Http\Controllers\Utils\Midi\MusiSONUtils;
use App\Http\Controllers\Utils\Midi\MidiMsg;

use Exception;

class MidiConvert {

    private $tmp_folder = "../storage/midi";
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

    public function toWav($midi_name, $out_name){
        

        $midi_path = $this->tmp_folder."/$midi_name.mid";

        $this->init();

        if(!file_exists(env("soundfont", $this->defaultSoundFont))){
          throw new Exception("No soundfont file found!");
        }

        if(!file_exists($midi_path)){
          throw new Exception("No midi file found!");
        }

        if(!is_dir($this->tmp_folder)){
          throw new Exception("Temp folder is not exists!");
        }

        $wav = $this->tmp_folder."/$out_name.wav";

        if(file_exists($wav)){
          if(file_exists($wav))unlink($wav);
        }

        $soundfont = env("soundfont", $this->defaultSoundFont);
        $gain = $this->gain;

        $cmd = "fluidsynth -F $wav $soundfont -g $gain $midi_path";
        exec($cmd);

        if(filesize($wav) < 1000) {

          if(file_exists($wav))unlink($wav);
          throw new Exception("Error converting to WAV, file is too small...");

        }

        return $wav;

    }

    public function toMP3($inName, $outName) {

        $wav = $this->tmp_folder."/$inName.wav";
        $mp3 = $this->tmp_folder."/$outName.mp3";

        $cmd = "ffmpeg -i $wav -ab 320k $mp3 2>&1";

        $out = [];
        $errCode = -1;

        if(file_exists($mp3)) {
            unlink($mp3);
        }

        exec($cmd, $out, $errCode);

        /*if(filesize($mp3_path)<1000){
          unlink($mp3_path);
          throw new Exception("Error when convert WAV, file too small");
        }*/

        if(file_exists($wav)) {
            unlink($wav);
        }

        return $mp3;
    }

}