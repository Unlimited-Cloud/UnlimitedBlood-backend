<?php

use App\Http\Controllers\DonorController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::put('/login', [DonorController::class, 'login']);

Route::put('/logout', [DonorController::class, 'logout']);

Route::put('/editProfile', [DonorController::class, 'editProfile']);

Route::get('/leaderboard', [DonorController::class, 'leaderboard']);

Route::get('/getRequests', [DonorController::class, 'getRequests']);

Route::post('/sendRequest', [DonorController::class, 'sendRequest']);


