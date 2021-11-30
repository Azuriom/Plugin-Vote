<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Illuminate\Support\Str;

class ServeurMinecraftVoteController extends Controller
{

    const SETTINGS_KEY = "vote::smv.key";

    public function index(){

        $key = Str::limit(setting(self::SETTINGS_KEY), 10);

        return view('vote::admin.smv.index', [
            'key' => $key,
        ]);
    }

}
