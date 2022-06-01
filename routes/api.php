<?php

use App\Http\Controllers\TestController;
use App\Http\Controllers\V1\AlbumController;
use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\ChartController;
use App\Http\Controllers\V1\ImageController;
use App\Http\Controllers\V1\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
        Route::post("/user/avatar", [UserController::class, "upload"]);
        Route::put("/users/{user}", [UserController::class, "update"]);
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
    $zipFile = storage_path("app" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "temp.zip");
    // $zip = new ZipArchive();


    if (!file_exists($zipFile)) {
        touch($zipFile);
    }
    // return file_exists($zipFile);
    $zip = new ZipArchive();

    $zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE);


    $file =  storage_path("app" . DIRECTORY_SEPARATOR . "images" . DIRECTORY_SEPARATOR . "1g5XHSMwUyXba6oPqsUAUfuKXAVZs5cQzDN4U4dI.png");
    $zip->addFile($file, "213.png");
    $zip->close();
    // return file_exists($zipFile);
    // fopen($zipFile, 'r');
    return Storage::download("images/temp.zip", null, ['Content-Type' => "blob"]);

    // return Storage::download("images/images.zip", null, ['Content-Type' => "blob"]);
    // if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
    //     // $files = File::files(public_path('myFiles'));
    //     // File
    //     // foreach ($files as $key => $value) {
    //     //     $relativeNameInZipFile = basename($value);
    // $zip->addFile($value, $relativeNameInZipFile)
    //     //     ;
    //     // }

    //     $zip->close();
    //     return response()->download($zipFile);
    // }

    return response()->download($zipFile);
    $myfile = fopen(storage_path("app/images" . DIRECTORY_SEPARATOR . "images.zip"), "w");
    fclose($myfile);
    return file_exists(storage_path("app/images" . DIRECTORY_SEPARATOR . "images.zip"));
    $zip = new ZipArchive();

    return file_exists(storage_path("images/images.zip"));
    dd(Storage::get("images/images.zip"));
    return Storage::download("images/images.zip", null, ['Content-Type' => "blob"]);
});

Route::any('{any}', function (Request $request) {
    $target_server = "http://wy.wan3guo.cn:7778/";
    $api = $target_server . $request->path();
    if ($request->isMethod('post')) {
        //

        // return  $request->all();

        // dd($request->header("authorization"));

        if ($request->header("authorization")) {
            $response = Http::withHeaders([
                "authorization" => ($request->header())["authorization"][0]
            ])->post($api, $request->all());
        } else {
            $response = Http::post($api, $request->all());
        }



        $header = $response->headers();
        if (key_exists("Authorization", $header)) {
            // return $header["Authorization"];
            return response()->json(json_decode($response->body()), 200, ["Authorization" => $header["Authorization"][0]]);
        }
        return json_decode($response->body());
        // return response()->json($response->body());
    }
    if ($request->isMethod('get')) {
        //
        $response = Http::post($api, $request->all());
        return $response;
    }
    // return ["data" => $request->all(), "api" => $request->path(), "url" => $request->url(), "fullUrl" => $request->fullUrl(), "method " => $request->method()];
})->where('any', '.*');
