<?php

namespace Azuriom\Plugin\Vote\Sites;

use Illuminate\Http\Request;
use ServeurMinecraftVote\ServeurMinecraftVote;

class ServeurMinecraftVoteWebhook
{

    public function webhook(Request $request){

        $smv = new ServeurMinecraftVote();

        $smv->verifyHeader();

        return "OK";
    }

}
