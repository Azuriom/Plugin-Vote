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
        $goalCommands = setting('vote.goal-commands');

        return view('vote::admin.settings', [
            'topPlayersCount' => setting('vote.top-players-count', 10),
            'displayRewards' => setting('vote.display-rewards', true),
            'ipCompatibility' => setting('vote.ipv4-v6-compatibility', true),
            'authRequired' => setting('vote.auth-required', false),
            'commands' => $commands ? json_decode($commands) : [],

            'goalEnabled' => (bool) setting('vote.goal-enabled', false),
            'goalTarget' => setting('vote.goal-target', 500),
            'goalCurrent' => (int) setting('vote.goal-current', 0),
            'goalAutoReset' => (bool) setting('vote.goal-auto-reset', false),
            'goalCommands' => $goalCommands ? json_decode($goalCommands) : [],
            'goalServers' => setting('vote.goal-servers') ? json_decode(setting('vote.goal-servers')) : [],
            'servers' => Server::executable()->get(),
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
            'goal_servers' => ['sometimes', 'nullable', 'array'],
        ]);

        $commands = $request->input('commands');
        $goalCommands = $request->input('goal_commands');
        $goalServers = $request->input('goal_servers');

        Setting::updateSettings([
            'vote.top-players-count' => $validated['top-players-count'],
            'vote.display-rewards' => $request->has('display-rewards'),
            'vote.ipv4-v6-compatibility' => $request->has('ip_compatibility'),
            'vote.auth-required' => $request->has('auth_required'),
            'vote.commands' => is_array($commands) ? json_encode(array_filter($commands)) : null,

            'vote.goal-enabled' => $request->has('goal_enabled'),
            'vote.goal-target' => $validated['goal_target'] ?? 500,
            'vote.goal-auto-reset' => $request->has('goal_auto_reset'),
            'vote.goal-commands' => is_array($goalCommands) ? json_encode(array_filter($goalCommands)) : null,
            'vote.goal-servers' => is_array($goalServers) ? json_encode(array_filter($goalServers)) : null,
        ]);

        ActionLog::log('vote.settings.updated');

        return to_route('vote.admin.settings')
            ->with('success', trans('messages.status.success'));
    }

    public function resetGoal()
    {
        Setting::updateSettings([
            'vote.goal-current' => 0,
        ]);

        ActionLog::log('vote.settings.updated');

        return to_route('vote.admin.settings')
            ->with('success', trans('messages.status.success'));
    }
}
