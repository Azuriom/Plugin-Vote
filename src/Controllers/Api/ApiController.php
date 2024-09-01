<?php

namespace Azuriom\Plugin\Vote\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Verification\VoteChecker;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function pingback(Request $request, string $site)
    {
        $checker = app(VoteChecker::class);

        return $checker->getVerificationForSite($site)->executePingbackCallback($request);
    }
}
