<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [Api\AuthController::class, 'login']);
Route::post('/register', [Api\AuthController::class, 'register']);


Route::get('auth/google/redirect', [Api\SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [Api\SocialAuthController::class, 'handleGoogleCallback']);

Route::get('auth/facebook/redirect', [Api\SocialAuthController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [Api\SocialAuthController::class, 'handleFacebookCallback']);

Route::middleware(['log.route', 'auth:sanctum'])->group(function () {
    Route::post('logout', [Api\AuthController::class, 'logout']);

    Route::get('/ups-data', [Api\UpsDataController::class, 'index']);
    Route::get('/ups-data/{id}', [Api\UpsDataController::class, 'show']);
    //Route::post('/ups-data/store', [Api\UpsDataController::class, 'store']);
});

Route::post('/ups-data/store', [Api\UpsDataController::class, 'store']);
