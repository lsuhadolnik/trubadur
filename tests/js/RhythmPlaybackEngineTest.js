var chai = require('chai');
var assert = chai.assert;

var manager = require("../../resources/assets/js/components/games/rhythm/rhythmPlaybackManager");
var RhythmPlaybackEngine  = require("../../resources/assets/js/components/games/rhythm/rhythmPlaybackEngine2");



describe("Fake manager playback engine tests", function() {

    let fakeChannel = {

        status: "playing",
        name: "helloChannel"

    }

    manager.channels["helloChannel"] = fakeChannel;

    let engine = new RhythmPlaybackEngine(manager, fakeChannel)


    it("Test: ChannelCanPlay", function(done) {

        if(manager._checkChannelCanPlay("helloChannel")){
            assert(false, "CHANNEL SHOULD NOT BE ABLE TO PLAY!");
        }

        done();

    });

})