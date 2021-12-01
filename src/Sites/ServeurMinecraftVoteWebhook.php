<?php

namespace Azuriom\Plugin\Vote\Sites;

use Azuriom\Plugin\Vote\Controllers\Admin\ServeurMinecraftVoteController;
use Illuminate\Http\Request;
use ServeurMinecraftVote\Exceptions\SignatureVerificationException;
use ServeurMinecraftVote\ServeurMinecraftVote;

class ServeurMinecraftVoteWebhook
{

    /**
     * @throws SignatureVerificationException
     */
    public function webhook(Request $request): string
    {

        $smv = new ServeurMinecraftVote();

        $key = setting(ServeurMinecraftVoteController::SETTINGS_WEBHOOK);

        if (empty($key)) {
            return "Impossible to recover the secret key of the webhook.";
        }

        $header = $request->header('X-SMV-Signature');
        $jsonData = $request['data'];
        $smv->verifyHeader($jsonData, $header, $key);

        $data = json_decode($jsonData);

        switch ($data->type) {
            case "user.follow":

                break;

        }

        return "OK";
    }

}
