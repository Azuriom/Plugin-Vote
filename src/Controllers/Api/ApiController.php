<?php

namespace Azuriom\Plugin\Vote\Controllers\Api;

use Illuminate\Http\Request;
use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Models\Pingback;
use Azuriom\Plugin\Vote\Verification\VoteChecker;

class ApiController extends Controller
{
    /**
     * https://gtop100.com/test/pingback.
     */
    public function pingback(Request $request, $site)
    {
        $checker = app(VoteChecker::class);
        $verifier = $checker->getVerificationForSite($site);
        $callback = $verifier->getPingbackCallback();
        $callback($request);

        return response()->noContent();
    }
}
