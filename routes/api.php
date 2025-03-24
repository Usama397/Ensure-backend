<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api;
use App\Http\Controllers\UpsRawDataController;

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
Route::post('/forgot-password', [Api\AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [Api\AuthController::class, 'resetPassword']);

Route::get('auth/google/redirect', [Api\SocialAuthController::class, 'redirectToGoogle']);
Route::get('auth/google/callback', [Api\SocialAuthController::class, 'handleGoogleCallback']);

Route::get('auth/facebook/redirect', [Api\SocialAuthController::class, 'redirectToFacebook']);
Route::get('auth/facebook/callback', [Api\SocialAuthController::class, 'handleFacebookCallback']);

Route::middleware(['log.route', 'auth:sanctum'])->group(function () {
    Route::post('logout', [Api\AuthController::class, 'logout']);
    Route::post('/delete-user/{id}', [Api\AuthController::class, 'deleteUser']);
    Route::post('/device-name', [Api\AuthController::class, 'saveDeviceName']);
    Route::post('/edit-user/profile', [Api\AuthController::class, 'updateProfile']);
    Route::get('/user/profile', [Api\AuthController::class, 'showProfile']);
    Route::get('/get-device-name', [Api\AuthController::class, 'getDeviceName']);

    Route::get('/ups-data', [Api\UpsDataController::class, 'index']);
    Route::get('/ups-data/{id}', [Api\UpsDataController::class, 'show']);


    Route::get('/history', [Api\UpsDataController::class, 'history']);
    Route::get('/ups-specifications', [Api\UpsDataController::class, 'userSpecificationsIndex']);





    Route::post('/find/unique-id', [Api\UpsDataController::class, 'findUniqueId']);
    //Route::post('/ups-data/store', [Api\UpsDataController::class, 'store']);

    Route::post('/toggle-settings', [Api\UpsDataController::class, 'saveSettings']);
    Route::get('/get-toggle-settings', [Api\UpsDataController::class, 'getSettings']);

    Route::get('/charging-status', [Api\UpsDataController::class, 'chargingStatus']);
});

Route::post('/ups-data/store', [Api\UpsDataController::class, 'store']);
Route::post('/ups-specifications/store', [Api\UpsDataController::class, 'userSpecificationsStore']);
Route::post('/device-charging', [Api\UpsDataController::class, 'deviceChargingStore']);
Route::post('/ups/raw-data', [UpsRawDataController::class, 'processRawData']);
Route::post('/ups/charging-status', [UPSController::class, 'receiveChargingStatus']);
