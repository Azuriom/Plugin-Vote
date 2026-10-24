<?php

namespace Azuriom\Plugin\Vote\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function pingback(Request $request, string $site)
    {
        $verifier = app(VoteChecker::class)->getVerificationForSite($site);

        if ($verifier === null || ! $verifier->hasPingback()) {
            return response()->noContent(404);
        }

        return $verifier->executePingbackCallback($request);
    }
}
