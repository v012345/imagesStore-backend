<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use OSS\OssClient;
use Ramsey\Uuid\Nonstandard\UuidV6;

class ImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return Image::paginate($request->per_page ?? 15);
        // return "index";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, OssClient $oss)
    // public function store(Request $request)
    {

        if ($request->has("images")) {
            $bucket = "market4scar";
            $date = date("Ymd");
            $images = [];
            foreach ($request->images as $key => $image) {
                $uuid = UuidV6::uuid6();
                $ext = $image->extension();
                $object = "{$date}/{$uuid}.{$ext}";
                $oss->uploadFile($bucket, $object, $image->path());
                $name = $image->getClientOriginalName();
                $size = $image->getSize();
                [$width, $height] = getimagesize($image->path());
                array_push($images, new Image([
                    "name" => $name,
                    "size" => $size,
                    "type" => $ext,
                    "width" => $width,
                    "height" => $height,
                    "uri" => $object,
                    "thumbnail_uri" => $object,
                ]));
            }
            $album = Album::firstOrCreate([
                "name" => $request->album ?? "all"
            ]);
            $album->images()->saveMany($images);
            // Image::insert($images);
            // return $album->images;
            return response("created", 201);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        return "show";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        return "update";
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        return "destroy";
    }
}
