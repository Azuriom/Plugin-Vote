<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin\Sites;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Server;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\WebhookReward;
use Azuriom\Plugin\Vote\Requests\RewardRequest;
use Azuriom\Plugin\Vote\Requests\WebhookRewardRequest;

class WebhookRewardController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vote::admin.rewards.webhooks.create', [
            'servers' => Server::executable()->get(),
            'webhooks' => ServeurMinecraftVoteController::WEBHOOK_EVENTS,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Azuriom\Plugin\Vote\Models\WebhookReward  $reward
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(WebhookRewardRequest $request)
    {
        WebhookReward::create($request->validated());

        return redirect()->route('vote.admin.smv.index')
            ->with('success', trans('vote::admin.rewards.status.created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Azuriom\Plugin\Vote\Models\WebhookReward  $reward
     * @return \Illuminate\Http\Response
     */
    public function edit(WebhookReward $reward)
    {
        return view('vote::admin.rewards.webhooks.edit', [
            'reward' => $reward->load('server'),
            'servers' => Server::executable()->get(),
            'webhooks' => ServeurMinecraftVoteController::WEBHOOK_EVENTS,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param WebhookRewardRequest $request
     * @param  \Azuriom\Plugin\Vote\Models\WebhookReward  $reward
     * @return \Illuminate\Http\Response
     */
    public function update(WebhookRewardRequest $request, WebhookReward $reward)
    {
        $reward->update($request->validated());

        return redirect()->route('vote.admin.smv.index')
            ->with('success', trans('vote::admin.rewards.status.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Azuriom\Plugin\Vote\Models\WebhookReward  $reward
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(WebhookReward $reward)
    {
        $reward->delete();

        return redirect()->route('vote.admin.smv.index')
            ->with('success', trans('vote::admin.rewards.status.deleted'));
    }
}
