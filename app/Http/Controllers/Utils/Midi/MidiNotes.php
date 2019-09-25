<?php

namespace App\Http\Controllers\Utils\Midi;

use Motniemtin\Midi\Midi;
use App\Http\Controllers\Utils\Midi\MusiSONUtils;
use App\Http\Controllers\Utils\Midi\MidiMsg as MSG;
use App\Http\Controllers\Utils\Midi\MidiConvert;

use App\Http\Controllers\API\RhythmExerciseController;

use Illuminate\Support\Facades\Auth;

use Exception;


class MidiNotes {

    public $noteForce = 60;

    public function GetExercise($exId, $BPMOverride = false) {

        $data = RhythmExerciseController::resolve($exId);

        if(!$data) {
            return null;
        }

        $data = (object) $data;

        $BPM = $BPMOverride ? $BPMOverride : $data->BPM;

        return $this->NotesToSound(
            $data->notes,
            (object) [
                "BPM" => $BPM,
                "bar" => $data->timeSignature,
                "pitch" => (object) [
                    "exercise" => [60],
                    "metronome" => [60, 70]
                ]
            ], true
        );

    }

    public function SetupMidi($BPM) {

        $midi = new Midi();
        $midi->open();

        $midi->setBPM($BPM);

        // Notes tract
        $trId = $midi->newTrack() - 1;

        $midi->addMsg($trId, MSG::Param(0, 1, 0, 121));
        $midi->addMsg($trId, MSG::Param(0, 1, 32, 0));
        $midi->addMsg($trId, MSG::ProgramChange(0, 1, 18));

        // Metronome track
        $trId = $midi->newTrack() - 1;

        $midi->addMsg($trId, MSG::Param(0, 1, 0, 121));
        $midi->addMsg($trId, MSG::Param(0, 1, 32, 0));
        $midi->addMsg($trId, MSG::ProgramChange(0, 1, 18));

        return $midi;

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
                    1,  // Channel
                    $sPitch, // Note
                    $this->noteForce  // Kok useka
                ));

                $midi->addMsg($trackInfo->trackId, MSG::Off(
                    ($engineDuration + $currentTime), 
                    1,  // Channel
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

        /*$userID = Auth::user();
        if(!$userID) {
            throw new Exception("No user logged in.");
        }
        $userID = $userID->id;*/
        $userID = 1;

        $midi = $this->SetupMidi($info->BPM);
        $this->GetMIDIData($midi, $notes, $info, (object) [
            "trackId" => 1,
            "pitch" => $info->pitch->exercise,
            "currentTime" => 0,
            "constDuration" => null
        ]);


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