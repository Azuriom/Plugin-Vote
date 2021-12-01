<?php

namespace Azuriom\Plugin\Vote\Sites;

use Azuriom\Plugin\Vote\Controllers\Admin\Sites\ServeurMinecraftVoteController;
use Azuriom\Plugin\Vote\Models\WebhookReward;
use Exception;
use Illuminate\Http\Request;
use ServeurMinecraftVote\Exceptions\SignatureVerificationException;
use ServeurMinecraftVote\ServeurMinecraftVote;

class ServeurMinecraftVoteWebhook
{

    /**
     * @throws SignatureVerificationException
     * @throws Exception
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

        $reward = WebhookReward::getRandomReward($data->type);

        if (empty($reward)){
            return "No reward found for $data->type";
        }

        $reward->giveTo($data->user);

        return "OK";
    }



}
