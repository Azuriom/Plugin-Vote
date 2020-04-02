<?php

/*
|--------------------------------------------------------------------------
| Helper functions
|--------------------------------------------------------------------------
|
| Here is where you can register helpers for your plugin. These
| functions are loaded by Composer and are globally available on the app !
| Just make sure you verify that a function don't exists before registering it
| to prevent any side effect.
|
*/

if (! function_exists('display_rewards')) {
    function display_rewards()
    {
        return setting('vote.display-rewards', true);
    }
}
