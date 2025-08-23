<?php

use Azuriom\Plugin\Vote\Controllers\Api\ApiController;
use Azuriom\Plugin\Vote\Controllers\Api\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your plugin. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/azlink', [VoteController::class, 'index'])->middleware('server.token');
Route::any('/pingback/{site}', [ApiController::class, 'pingback'])->name('sites.pingback');
