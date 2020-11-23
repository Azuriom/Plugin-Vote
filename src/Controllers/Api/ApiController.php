<?php

namespace Azuriom\Plugin\Vote\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Vote\Models\Pingback;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    /**
     * https://gtop100.com/test/pingback.
     */
    public function gtop100(Request $request)
    {
        abort_if(! in_array($request->ip(), ['198.148.82.98', '198.148.82.99']), 403);

        if ($request->Successful == '0') {
            Pingback::create(['domain'=>'gtop100.com', 'ip'=> $request->VoterIP]);
        }

        return response()->noContent();
    }
}
