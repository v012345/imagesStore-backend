<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\ChartController;
use App\Http\Controllers\V1\ImageController;
use App\Http\Controllers\V1\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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
        Route::put("/users/{user}",[UserController::class, "update"]);
        Route::apiResource('images', ImageController::class);
        Route::apiResource('albums', AlbumController::class);
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/download/image', [ImageController::class, 'download']);
        Route::get('/download/album', [AlbumController::class, 'download']);
        Route::get('/statistics/data', [ChartController::class, 'data']);
    });
});

Route::any("test", function (Request $request) {
    // return 123;
    $myfile = fopen(storage_path("images" . DIRECTORY_SEPARATOR . "images.zip"), "w");
    fclose($myfile);
    return file_exists(storage_path("images" . DIRECTORY_SEPARATOR . "images.zip"));
    $zip = new ZipArchive();

    return file_exists(storage_path("images/images.zip"));
    dd(Storage::get("images/images.zip"));
    return Storage::download("images/images.zip", null, ['Content-Type' => "blob"]);
});
