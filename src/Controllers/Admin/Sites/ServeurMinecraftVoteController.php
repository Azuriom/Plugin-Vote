<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin\Sites;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Vote\Models\WebhookHistory;
use Azuriom\Plugin\Vote\Models\WebhookReward;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ServeurMinecraftVote\Exceptions\WebhookCreateException;
use ServeurMinecraftVote\ServeurMinecraftVote;

class ServeurMinecraftVoteController extends Controller
{
    const SETTINGS_KEY = 'vote::smv.key';
    const SETTINGS_WEBHOOK = 'vote::smv.webhook';

    const WEBHOOK_EVENTS = [
        'user.follow',
        'user.unfollow',
        'train.start',
        'train.finish',
        'train.levelup',
        'train.vote',
    ];

    public function index()
    {
        $key = Str::limit(setting(self::SETTINGS_KEY), 20);

        return view('vote::admin.smv.index', [
            'key' => $key,
            'rewards' => WebhookReward::with('server')->get(),
            'logs' => WebhookHistory::with('webhook')->latest()->paginate(),
        ]);
    }

    /**
     * Create Webhook.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $this->validate($request, [
            'key' => ['required', 'min:30', 'starts_with:smv_sk_'],
        ]);

        $secretKey = $request['key'];
        try {
            $smv = new ServeurMinecraftVote($secretKey);

            $url = route('vote.api.sites.webhooks', ['site' => 'smv']);
            $webhooks = $smv->getWebhooks();

            foreach ($webhooks as $webhook) {
                if ($webhook->endpoint == $url) {
                    return redirect()->route('vote.admin.smv.index')
                        ->with('error', trans('vote::admin.smv.webhook.already'));
                }
            }

            $webhook = $smv->createWebhook($url, self::WEBHOOK_EVENTS, setting('name', 'Azuriom'));

            $setting = [
                self::SETTINGS_KEY => $secretKey,
                self::SETTINGS_WEBHOOK => $webhook->secretKey,
            ];
            Setting::updateSettings($setting);

            return redirect()->route('vote.admin.smv.index')
                ->with('success', trans('vote::admin.smv.webhook.success'));
        } catch (GuzzleException|WebhookCreateException $e) {
            return redirect()->route('vote.admin.smv.index')
                ->with('error', trans('vote::admin.smv.webhook.error'));
        }
    }
}
