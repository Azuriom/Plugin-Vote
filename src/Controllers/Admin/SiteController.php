<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\Site;
use Azuriom\Plugin\Vote\Requests\SiteRequest;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('vote::admin.sites.index', ['sites' => Site::all()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $checker = app(VoteChecker::class);

        return view('vote::admin.sites.create', [
            'rewards' => Reward::all(),
            'sites' => $checker->getSites(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(SiteRequest $request)
    {
        $site = Site::create(Arr::except($request->validated(), 'rewards'));

        $site->rewards()->sync($request->input('rewards', []));

        return to_route('vote.admin.sites.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Site $site)
    {
        return view('vote::admin.sites.edit', [
            'rewards' => Reward::all(),
            'site' => $site->load('rewards'),
            'sites' => app(VoteChecker::class)->getSites(),
        ]);
    }

    public function verificationForUrl(Request $request)
    {
        $voteUrl = $request->query('url');

        if ($voteUrl === null) {
            return response()->json(['message' => 'Invalid URL'], 422);
        }

        $checker = app(VoteChecker::class);

        $host = $checker->parseHostFromUrl($voteUrl);

        if ($host === null) {
            return response()->json(['message' => 'Invalid URL'], 422);
        }

        if (! $checker->hasVerificationForSite($host)) {
            return response()->json([
                'domain' => $host,
                'info' => trans('vote::admin.sites.verifications.disabled'),
                'supported' => false,
            ]);
        }

        $verifier = $checker->getVerificationForSite($host);

        if (! $verifier->requireVerificationKey()) {
            return response()->json([
                'domain' => $host,
                'info' => trans('vote::admin.sites.verifications.auto'),
                'supported' => true,
                'automatic' => true,
            ]);
        }

        $message = trans('vote::admin.sites.verifications.input');

        if ($verifier->hasPingback()) {
            $message .= ' '.trans('vote::admin.sites.verifications.pingback', [
                'url' => route('vote.api.sites.pingback', $host),
            ]);
        }

        return response()->json([
            'domain' => $host,
            'info' => $message,
            'supported' => true,
            'automatic' => false,
            'label' => trans('vote::admin.sites.verifications.'.$verifier->verificationTypeKey()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(SiteRequest $request, Site $site)
    {
        $site->update(Arr::except($request->validated(), 'rewards'));

        $site->rewards()->sync($request->input('rewards', []));

        return to_route('vote.admin.sites.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws \LogicException
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return to_route('vote.admin.sites.index')
            ->with('success', trans('messages.status.success'));
    }
}
