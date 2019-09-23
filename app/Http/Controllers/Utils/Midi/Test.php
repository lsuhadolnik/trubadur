<?php

namespace App\Http\Controllers\Utils\Midi;

use Motniemtin\Midi\Midi;
use MusiSONUtils;
use MidiMsg;


class MidiTest {


    public function Test() {

        
        $msg = new MidiMsg();


        $musison_string = <<<KK
        {
            "id": 1165,
            "BPM": 70,
            "name": null,
            "timeSignature": {
                "base_note": 8,
                "num_beats": 6
            },
            "notes": [
                {
                    "dot": true,
                    "type": "n",
                    "value": 4
                },
                {
                    "dot": true,
                    "type": "n",
                    "value": 4
                },
                {
                    "type": "bar"
                },
                {
                    "dot": true,
                    "tie": true,
                    "type": "n",
                    "value": 2
                }
            ]
        }
        KK;

        $musison = json_decode($musison_string);
        $util = new MusiSONUtils($musison->notes);

        $midi = new Midi();
        $midi->open();

        $trId = $midi->newTrack() - 1;
        echo $msg->On(0, 1, 64, 60);
        $midi->addMsg($trId, $msg->On(1, 1, 64, 60));
        $midi->addMsg($trId, $msg->Off(20, 1,64,60));

        echo $util->toDurations();

    }

}