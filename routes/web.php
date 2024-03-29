<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function (Request $request) {
    if($request->name){
        return view('welcome', ['name' => $request->name]);
    }
    return view('welcome', ['name' => '...']);
    // return view('welcome');
});

// Route::fallback(function (Request $request) {
//     //
//     return ["data" => $request->all(), "api" => $request->path(), "url" => $request->url(), "fullUrl" => $request->fullUrl(), "method " => $request->method()];
// });
