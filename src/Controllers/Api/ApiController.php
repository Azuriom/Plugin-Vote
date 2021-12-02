<?php

namespace Azuriom\Plugin\Vote\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Controllers\Admin\Sites\ServeurMinecraftVoteController;
use Azuriom\Plugin\Vote\Models\WebhookHistory;
use Azuriom\Plugin\Vote\Models\WebhookReward;
use Azuriom\Plugin\Vote\Sites\ServeurMinecraftVoteWebhook;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Exception;
use Illuminate\Http\Request;
use ServeurMinecraftVote\Exceptions\SignatureVerificationException;
use ServeurMinecraftVote\ServeurMinecraftVote;

class ApiController extends Controller
{
    public function pingback(Request $request, string $site)
    {
        $checker = app(VoteChecker::class);
        $verifier = $checker->getVerificationForSite($site);

        return $verifier->executePingbackCallback($request) ?? response()->noContent();
    }

    /**
     * @param Request $request
     * @param string $site
     * @return string
     * @throws Exception
     */
    public function webhooks(Request $request, string $site)
    {

        if ($site !== 'smv') {
            return json_encode([
                'status' => 'error',
                'message' => 'Website not found',
            ]);
        }

        try {


            $smv = new ServeurMinecraftVote();

            $key = setting(ServeurMinecraftVoteController::SETTINGS_WEBHOOK);

            if (empty($key)) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'Impossible to recover the secret key of the webhook.',
                ]);
            }

            $header = $request->header('X-SMV-Signature');
            $jsonData = $request['data'];
            $jsonData = is_array($jsonData) ? json_encode($jsonData) : $jsonData;

            //   $smv->verifyHeader($jsonData, $header, $key);

            $type = $request['type'];
            $data = $request['data'];
            $reward = WebhookReward::getRandomReward($type, $data['user']['name'] ?? '');

            if (empty($reward)) {
                return "No reward found for $type";
            }
            
            $reward->giveTo($data['user']['name'] ?? '');

            if ($reward->limit !== 0) {
                WebhookHistory::create([
                    'webhook_reward_id' => $reward->id,
                    'name' => $data['user']['name'] ?? '',
                ]);
            }

            return json_encode([
                'status' => 'success',
                'message' => 'OK',
            ]);
        } catch (SignatureVerificationException $exception) {
            return json_encode([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }
    }

}
