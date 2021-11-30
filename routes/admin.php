<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your plugin. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" and "admin" middleware groups. Now create a great admin panel !
|
*/

Route::middleware('can:vote.admin')->group(function () {
    Route::get('/settings', 'SettingController@show')->name('settings');
    Route::post('/settings', 'SettingController@save')->name('settings.save');

    Route::get('sites/verification', 'SiteController@verificationForUrl')->name('sites.verification');

    Route::resource('sites', 'SiteController')->except('show');
    Route::resource('rewards', 'RewardController')->except('show');
    Route::resource('votes', 'VoteController')->only('index');

    Route::prefix('serveur/minecraft/vote')->name('smv.')->group(function (){
        Route::get('/', 'ServeurMinecraftVoteController@index')->name('index');
        Route::post('/store', 'ServeurMinecraftVoteController@store')->name('store');
    });
});
