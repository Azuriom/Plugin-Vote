<?php

namespace Azuriom\Plugin\Vote\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;

class ServeurMinecraftVoteController extends Controller
{

    public function index(){
        return view('vote::admin.smv.index');
    }

}
