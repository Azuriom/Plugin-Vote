<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Server;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Requests\RewardRequest;
use Illuminate\Support\Arr;

class RewardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vote::admin.rewards.index', [
            'rewards' => Reward::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('vote::admin.rewards.create', [
            'servers' => Server::executable()->get(),
            'cron' => function_exists('scheduler_running') && scheduler_running(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RewardRequest $request)
    {
        $reward = Reward::create(Arr::except($request->validated(), 'servers'));

        $reward->servers()->sync($request->input('servers', []));

        if ($request->hasFile('image')) {
            $reward->storeImage($request->file('image'), true);
        }

        return to_route('vote.admin.rewards.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Reward $reward)
    {
        return view('vote::admin.rewards.edit', [
            'reward' => $reward->load('servers'),
            'servers' => Server::executable()->get(),
            'cron' => function_exists('scheduler_running') && scheduler_running(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RewardRequest $request, Reward $reward)
    {
        $reward->update(Arr::except($request->validated(), 'servers'));

        $reward->servers()->sync($request->input('servers', []));

        if ($request->hasFile('image')) {
            $reward->storeImage($request->file('image'), true);
        }

        return to_route('vote.admin.rewards.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \LogicException
     */
    public function destroy(Reward $reward)
    {
        $reward->delete();

        return to_route('vote.admin.rewards.index')
            ->with('success', trans('messages.status.success'));
    }
}
