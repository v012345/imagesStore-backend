<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Jobs\UploadImage;
use App\Models\Album;
use App\Models\Image;
use Intervention\Image\Facades\Image as ImageEditor;
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

        return Image::with("albums")->whereHas("albums", function ($query) use ($request) {
            $query->where('albums.id', $request->album);
        })->paginate($request->per_page ?? 15);
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
            // $bucket = "market4scar";
            $date = date("Ymd");
            $images = [];

            foreach ($request->images as $key => $image) {

                // 原图
                $uuid = UuidV6::uuid6();
                $ext = $image->extension();
                $object_src = "{$date}/{$uuid}.{$ext}";
                $name = $image->getClientOriginalName();
                $size = $image->getSize();
                [$width, $height] = getimagesize($image->path());
                $original_image_path  =  storage_path("app/" . $image->storeAs('images', "{$uuid}.{$ext}"));





                // $oss->uploadFile($bucket, $object_src, $image->path());

                UploadImage::dispatch([
                    "object" => $object_src,
                    "path" => $original_image_path
                ]);


                $uuid = UuidV6::uuid6();
                $canvas = ImageEditor::canvas(300, 200);
                $thumbnail =  ImageEditor::make($image->path());

                $thumbnail->resize(300, 200, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $canvas->insert($thumbnail, 'center');
                $object_thumbnail = "{$date}/{$uuid}.{$ext}";
                $temp_file = storage_path("app/public/{$uuid}.{$ext}");
                $canvas->save($temp_file);
                $canvas->destroy();
                $thumbnail->destroy();
                $oss->uploadFile($bucket, $object_thumbnail, $temp_file);
                // UploadImage::dispatch([
                //     "object" => $object_thumbnail,
                //     "path" => $temp_file
                // ]);

                unlink($temp_file);

                array_push($images, new Image([
                    "name" => $name,
                    "size" => $size,
                    "type" => $ext,
                    "width" => $width,
                    "height" => $height,
                    "uri" => $object_src,
                    "thumbnail_uri" => $object_thumbnail,
                ]));
            }
            $album = Album::find($request->album);
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
        $image = Image::find($id);
        $image->albums()->detach();
        $image->delete();
        return response("", "204");
    }
}
