<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Display the vote settings page.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $commands = setting('vote.commands');

        return view('vote::admin.settings', [
            'topPlayersCount' => setting('vote.top-players-count', 10),
            'ipCompatibility' => setting('vote.ipv4-v6-compatibility', true),
            'commands' => $commands ? json_decode($commands) : [],
        ]);
    }

    /**
     * Update the settings.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function save(Request $request)
    {
        $validated = $this->validate($request, [
            'top-players-count' => ['numeric', 'min:1'],
            'commands' => ['sometimes', 'nullable', 'array'],
        ]);

        $commands = $request->input('commands');

        Setting::updateSettings([
            'vote.top-players-count' => $validated['top-players-count'],
            'vote.display-rewards' => $request->has('display-rewards'),
            'vote.ipv4-v6-compatibility' => $request->has('ip_compatibility'),
            'vote.commands' => is_array($commands) ? json_encode(array_filter($commands)) : null,
        ]);

        return redirect()->route('vote.admin.settings')
            ->with('success', trans('messages.status.success'));
    }
}
