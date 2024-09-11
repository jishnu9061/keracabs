<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['namespace' => 'Api', 'name' => 'api'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::group(['middleware' => ['auth:api']], function () {
        Route::get('home', [AuthController::class, 'homePage']);
    });
});
