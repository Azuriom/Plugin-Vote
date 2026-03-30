<?php

use Azuriom\Plugin\Vote\Controllers\Admin\RewardController;
use Azuriom\Plugin\Vote\Controllers\Admin\SettingController;
use Azuriom\Plugin\Vote\Controllers\Admin\SiteController;
use Azuriom\Plugin\Vote\Controllers\Admin\VoteController;
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
    Route::get('/settings', [SettingController::class, 'show'])->name('settings');
    Route::post('/settings', [SettingController::class, 'save'])->name('settings.save');

    Route::get('sites/verification', [SiteController::class, 'verificationForUrl'])->name('sites.verification');

    Route::resource('sites', SiteController::class)->except('show');
    Route::resource('rewards', RewardController::class)->except('show');
    Route::resource('votes', VoteController::class)->only('index');
});
