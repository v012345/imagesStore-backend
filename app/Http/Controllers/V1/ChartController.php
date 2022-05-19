<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    //
    public function data()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $albums = $user->albums()->withCount("images")->orderByDesc("id")->limit(3)->get();
        $images = $user->albums()->images;
        return ["albums" => $albums, "images" => $images];
    }
}
