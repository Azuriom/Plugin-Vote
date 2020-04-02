<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * @return Factory|View
     */
    public function show()
    {
        return view('vote::admin.settings', [
            'topPlayersCount' => setting('vote.top-players-count', 10),
        ]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function save(Request $request)
    {
        Setting::updateSettings('vote.top-players-count', $this->validate($request, [
            'top-players-count' => ['numeric', 'min:5', 'max:100'],
        ])['top-players-count']);
        Setting::updateSettings('vote.display-rewards', $request->has('display-rewards'));

        return redirect()->route('vote.admin.settings')
            ->with('success', trans('admin.settings.status.updated'));
    }
}
