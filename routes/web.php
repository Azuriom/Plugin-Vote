<?php

use Azuriom\Plugin\Vote\Controllers\VoteController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your plugin. These
| routes are loaded by the RouteServiceProvider of your plugin within
| a group which contains the "web" middleware group and your plugin name
| as prefix. Now create something great!
|
*/

Route::get('/', [VoteController::class, 'index'])->name('home');
Route::get('/user/{user}', [VoteController::class, 'verifyUser'])->name('verify-user');
Route::post('/site/{site}', [VoteController::class, 'vote'])->name('vote');
Route::post('/site/{site}/done', [VoteController::class, 'done'])->name('done');
