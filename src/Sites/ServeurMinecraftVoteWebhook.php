<?php

namespace Azuriom\Plugin\Vote\Sites;

use Azuriom\Plugin\Vote\Controllers\Admin\Sites\ServeurMinecraftVoteController;
use Azuriom\Plugin\Vote\Models\WebhookHistory;
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
        $jsonData = is_array($jsonData) ? json_encode($jsonData) : $jsonData;

        $smv->verifyHeader($jsonData, $header, $key);

        $type= $request['type'];
        $data = $request['data'];
        $reward = WebhookReward::getRandomReward($type, $data['user']['name']);

        if (empty($reward)) {
            return "No reward found for $type";
        }

        $reward->giveTo($data['user']['name'] ?? '');

        WebhookHistory::create([
            'webhook_reward_id' => $reward->id,
            'name' => $data['user']['name'],
        ]);

        return "OK";
    }


}
