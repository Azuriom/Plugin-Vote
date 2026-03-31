<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\ActionLog;
use Azuriom\Models\Server;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the vote settings page.
     */
    public function show()
    {
        $commands = setting('vote.commands');
        $goalCommands = setting('vote.goal.commands');
        $goalTarget = setting('vote.goal.target');

        return view('vote::admin.settings', [
            'topPlayersCount' => setting('vote.top-players-count', 10),
            'displayRewards' => setting('vote.display-rewards', true),
            'ipCompatibility' => setting('vote.ipv4-v6-compatibility', true),
            'authRequired' => setting('vote.auth-required', false),
            'commands' => $commands ? json_decode($commands) : [],
            'goalEnabled' => $goalTarget !== null && $goalTarget > 0,
            'goalTarget' => $goalTarget,
            'goalAutoReset' => (bool) setting('vote.goal.auto_reset', false),
            'goalCommands' => $goalCommands ? json_decode($goalCommands) : [],
            'servers' => Server::executable()->get()->pluck('name', 'id'),
        ]);
    }

    /**
     * Update the settings.
     */
    public function save(Request $request)
    {
        $validated = $this->validate($request, [
            'top-players-count' => ['numeric', 'min:1'],
            'commands' => ['sometimes', 'nullable', 'array'],
            'goal_target' => ['nullable', 'numeric', 'min:1'],
            'goal_commands' => ['sometimes', 'nullable', 'array'],
        ]);

        $commands = $request->input('commands');
        $goalCommands = $request->input('goal_commands');
        $goalEnabled = $request->filled('goal_enabled');

        Setting::updateSettings([
            'vote.top-players-count' => $validated['top-players-count'],
            'vote.display-rewards' => $request->has('display-rewards'),
            'vote.ipv4-v6-compatibility' => $request->has('ip_compatibility'),
            'vote.auth-required' => $request->has('auth_required'),
            'vote.commands' => is_array($commands) ? json_encode(array_filter($commands)) : null,
            'vote.goal.target' => $goalEnabled ? $validated['goal_target'] : null,
            'vote.goal.auto_reset' => $goalEnabled ? $request->has('goal_auto_reset') : null,
            'vote.goal.commands' => $goalEnabled && is_array($goalCommands) ? json_encode(array_filter($goalCommands)) : null,
        ]);

        ActionLog::log('vote.settings.updated');

        return to_route('vote.admin.settings')
            ->with('success', trans('messages.status.success'));
    }
}
