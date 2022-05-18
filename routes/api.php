<?php

use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\ImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix("v1")->group(function () {

    Route::post("auth/register", [AuthController::class, "signup"]);
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::group(['middleware' => ['auth:sanctum']], function () {
        Route::get('/me', function (Request $request) {
            return auth()->user();
        });
        Route::apiResource('images', ImageController::class);
        Route::apiResource('albums', AlbumController::class);
        // Route::post('/auth/logout', [AuthController::class, 'logout']);
    });
});
