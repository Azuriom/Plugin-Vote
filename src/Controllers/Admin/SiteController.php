<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Models\Reward;
use Azuriom\Plugin\Vote\Models\Site;
use Azuriom\Plugin\Vote\Requests\SiteRequest;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('vote::admin.sites.index', ['sites' => Site::all()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vote::admin.sites.create', ['rewards' => Reward::all()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Azuriom\Plugin\Vote\Requests\SiteRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SiteRequest $request)
    {
        $site = Site::create($request->validated());

        $rewards = array_keys($request->input('rewards', []));

        $site->rewards()->sync($rewards);

        return redirect()->route('vote.admin.sites.index')
            ->with('success', trans('vote::admin.sites.status.created'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \Azuriom\Plugin\Vote\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function edit(Site $site)
    {
        return view('vote::admin.sites.edit', [
            'rewards' => Reward::all(),
            'site' => $site->load('rewards'),
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
                'info' => trans('vote::admin.sites.no-verification'),
                'supported' => false,
            ]);
        }

        $verifier = $checker->getVerificationForSite($host);

        if (! $verifier->requireVerificationKey()) {
            $message = trans('vote::admin.sites.auto-verification');

            if ($checker->hasPingback($host)) {
                $message .= ' '.trans('vote::admin.sites.verifications.pingback', [
                    'url' => route('vote.api.sites.pingback', $host),
                ]);
            }

            return response()->json([
                'domain' => $host,
                'info' => $message,
                'supported' => true,
                'automatic' => true,
            ]);
        }

        return response()->json([
            'domain' => $host,
            'info' => trans('vote::admin.sites.key-verification'),
            'supported' => true,
            'automatic' => false,
            'label' => trans('vote::admin.sites.verifications.'.$verifier->verificationTypeKey()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Azuriom\Plugin\Vote\Requests\SiteRequest  $request
     * @param  \Azuriom\Plugin\Vote\Models\Site  $site
     * @return \Illuminate\Http\Response
     */
    public function update(SiteRequest $request, Site $site)
    {
        $site->update($request->validated());

        $rewards = array_keys($request->input('rewards', []));

        $site->rewards()->sync($rewards);

        return redirect()->route('vote.admin.sites.index')
            ->with('success', trans('vote::admin.sites.status.updated'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Azuriom\Plugin\Vote\Models\Site  $site
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Site $site)
    {
        $site->delete();

        return redirect()->route('vote.admin.sites.index')
            ->with('success', trans('vote::admin.sites.status.deleted'));
    }
}
