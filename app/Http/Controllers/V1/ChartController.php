<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Illuminate\Http\Request;

class ChartController extends Controller
{
    //
    public function data()
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        $albums = $user->albums()->withCount("images")->orderByDesc("id")->limit(3)->get();
        $cdn = Image::where("has_uploaded_to_cdn", true)->sum("size");
        $local = intval(Image::where("has_uploaded_to_cdn", false)->sum("size"));
        return ["albums" => $albums, "images" => [
            "cdn" => $cdn,
            "local" => $local,
        ]];
    }
}
