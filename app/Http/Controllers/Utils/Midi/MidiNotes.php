<?php

namespace App\Http\Controllers\Utils\Midi;

use Motniemtin\Midi\Midi;
use App\Http\Controllers\Utils\Midi\MusiSONUtils;
use App\Http\Controllers\Utils\Midi\MidiMsg as MSG;
use App\Http\Controllers\Utils\Midi\MidiConvert;

use App\Http\Controllers\API\RhythmExerciseController;

use Illuminate\Support\Facades\Response;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;

use Exception;


class MidiNotes {

    public $noteForce = 60;

    public function GetExercise($exId) {

        $data = RhythmExerciseController::resolve($exId);

        if(!$data) {
            return null;
        }
        $data = (object) $data;


        $enableMetronome = false;
        $rawMetronome = Input::get('metronome');
        if($rawMetronome === 'true') {
            $enableMetronome = true;
        }

        $rawBPMOverride = Input::get('bpm');
        $BPMOverride = ctype_digit($rawBPMOverride) ? intval($rawBPMOverride) : false;
        $BPM = $BPMOverride ? $BPMOverride : $data->BPM;

        
        $file = $this->NotesToSound(
            $data->notes,
            (object) [
                "enableMetronome" => $enableMetronome,
                "BPM" => $BPM,
                "bar" => $data->timeSignature,
                "pitch" => (object) [
                    "exercise" => [69],
                    "metronome" => [60, 70]
                ]
            ], true
        );

        $response = Response::make($file, 200);
        $response->header('Content-Type', 'audio/mpeg');
        return $response;

    }

    public function SetupMidi($BPM) {

        $midi = new Midi();
        $midi->open();

        $tempo = 480000 * 60 / $BPM;

        $midi->setTempo($tempo);

        // Notes tract
        $midi->newTrack();

        // Metronome track
        $midi->newTrack();
        

        return $midi;

    }

    public function Instrument($midi, $trId, $xylo = false) {

        $i = $xylo ? 13 : 18;

        $midi->addMsg($trId, MSG::Param(0, $trId, 0, 121));
        $midi->addMsg($trId, MSG::Param(0, $trId, 32, 0));
        $midi->addMsg($trId, MSG::ProgramChange(0, $trId, $i));

    }

    /*
    info {
        BPM: 60,
        bar: [
            base_note: 4,
            num_beats: 4
        ],
        pitch: [
            exercise: [],
            metronome: []
        ],
    }
    */

    /*
    trackInfo {
        currentTime: 0
        trackId: 1,
        pitch: [],
        constDuration: null,
    }
    */


    public function GetMetronomePitches($bar, $num_bars = 1) {
    
        // Original
        // [93, 86];
    
        $hi = 100;
        $lo = 76;
    
        $pitches = [];
    
        for($ooo = 0; $ooo < $num_bars; $ooo++){
            if(isset($bar->subdivisions)){
    
                foreach($bar->subdivisions as $s){
                    $pitches[] = $hi;
                    for ($i = 1; $i < $s->n; $i++) { $pitches[] = $lo; }
                }
    
            } else if(!isset($bar->subdivisions) && $bar->base_note == 8 && $bar->num_beats == 6) {
    
                // Special counting for 6/8 time...
                $pitches = array_merge($pitches, [$hi, $lo, $lo, $hi, $lo, $lo]);
    
            } 
            else if(!isset($bar->subdivisions) && $bar->base_note == 8 && $bar->num_beats == 9) {
    
                // Special counting for 6/8 time...
                $pitches = array_merge($pitches, [$hi, $lo, $lo, $hi, $lo, $lo, $hi, $lo, $lo]);
    
            }else {
                $pitches[] = ($hi);
                for ($i = 1; $i < $bar->num_beats; $i++) { $pitches[] = $lo; }
            }
        }
    
        return $pitches;
    
    }
    
    public function GetMetronomeNotes($bar, $num_bars = 1){
    
        $countInNotes = [];
    
        for($vv = 0; $vv < $num_bars; $vv++){
            if(isset($bar->subdivisions)){
                foreach($bar->subdivisions as $sd) {
                    for($i = 0; $i < $sd->n; $i++){
                        $countInNotes[] = (object) [ "type" => 'n', "value" => $sd->d ];
                    }
                }

            } else {
                for($i = 0; $i < $bar->num_beats; $i++){
                    $countInNotes[] = (object)[ "type" => 'n', "value" => $bar->base_note ];
                }
            }
        }
    
        return $countInNotes;
    }
    
    public function GetMIDIData($midi, $notes, $info, $trackInfo) {

        $durs = MusiSONUtils::toDurations($notes);

        $currentTime = $trackInfo->currentTime;

        $currentNoteID = 0;
        foreach($durs as $dur) {

            $realDuration = $dur->toFloat() * $info->bar->base_note;

            if($realDuration > 0)
            {
                $pitchL = count($trackInfo->pitch) - 1;
                $sPitch = $trackInfo->pitch[min($pitchL, $currentNoteID)];

                $engineDuration = ($realDuration - 0.05);
                
                if($trackInfo->constDuration != null) {
                    $engineDuration = $trackInfo->constDuration;
                }

                $midi->addMsg($trackInfo->trackId, MSG::On(
                    $currentTime,  // Timestamp
                    $trackInfo->trackId,  // Channel
                    $sPitch, // Note
                    $trackInfo->noteForce  // Kok useka
                ));

                $midi->addMsg($trackInfo->trackId, MSG::Off(
                    ($engineDuration + $currentTime), 
                    $trackInfo->trackId,  // Channel
                    $sPitch, // Note
                    0  // Kok useka
                ));

            }
            else 
            {
                // Rest or unknown
                $realDuration = -$realDuration;
            }

            $currentTime += $realDuration;

            $currentNoteID++;
        }

        return $currentTime;
    }

    public function NotesToSound($notes, $info, $convertToMP3) {

        $userID = Auth::user();
        if(!$userID) {
            throw new Exception("No user logged in.");
        }
        $userID = $userID->id;

        $countinNotes = $this->GetMetronomeNotes($info->bar,   1);
        $countinPitch = $this->GetMetronomePitches($info->bar, 1);

        $metronomeNotes = $this->GetMetronomeNotes($info->bar,   2);
        $metronomePitch = $this->GetMetronomePitches($info->bar, 2);

        $midi = $this->SetupMidi($info->BPM);

        $timeDiff = 0;


        // Countdown
        $this->Instrument($midi, 2, true);
        $timeDiff = $this->GetMIDIData($midi, $countinNotes, $info, (object) [
            "trackId" => 2,
            "pitch" => $countinPitch,
            "currentTime" => 0,
            "constDuration" => 0.2,
            "noteForce" => 80,
        ]);
        

        // Melody
        $this->Instrument($midi, 1, false);
        $this->GetMIDIData($midi, $notes, $info, (object) [
            "trackId" => 1,
            "pitch" => $info->pitch->exercise,
            "currentTime" => $timeDiff,
            "constDuration" => null,
            "noteForce" => 50,
        ]);

        // Metronome
        if($info->enableMetronome) {
            $this->Instrument($midi, 2, true);
            $this->GetMIDIData($midi, $metronomeNotes, $info, (object) [
                "trackId" => 2,
                "pitch" => $metronomePitch,
                "currentTime" => $timeDiff,
                "constDuration" => 0.2,
                "noteForce" => 90,
            ]);
        }
        
        $midi->saveMidFile("../storage/midi/$userID.mid");
        
        $c = new MidiConvert();
        $wav = $c->toWav($userID, $userID);

        if(!$convertToMP3) return file_get_contents($wav);


        $mp3 = $c->toMP3($userID, $userID);
        return file_get_contents($mp3);

        // $midiData = $midi->getMid();
        // return $midiData;

    }

}