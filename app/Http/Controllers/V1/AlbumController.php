<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Ramsey\Uuid\Uuid;
use ZipArchive;


class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->albums()->with(["images" => function ($query) {
            // $query->take(8);
        }])->withCount("images")->orderByDesc("id")->get();
    }
    // select `albums`.*, (select count(*) from `images` inner join `album_image` on `images`.`id` = `album_image`.`image_id` where `albums`.`id` = `album_image`.`album_id`) as `images_count` from `albums` inner join `album_user` on `albums`.`id` = `album_user`.`album_id` where `album_user`.`user_id` = ? order by `id` desc
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        if ($request->name) {
            $album = Album::firstOrCreate([
                'name' => $request->name
            ]);
            $album->users()->syncWithoutDetaching(auth()->user());
            return $album;
        } else {
            return response("miss name", 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Request $request)
    {
        //
        $album = Album::find($id);
        if ($album) {
            return  $album->images()->paginate($request->per_page ?? 15);
        } else {
            return response("", 404);
        }
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
        $album = Album::find($id);
        if ($album) {
            if ($request->name && strlen($request->name) > 0) {
                $album->name = $request->name;
                $album->save();
            }

            //     return  $album->images()->paginate($request->per_page ?? 15);
            // } else {
            //     return response("", 404);
        }
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
    }

    public function download(Request $request)
    {

        $album = Album::find($request->id);
        $fileName = Uuid::uuid6();
        if ($album) {
            $zipFile = storage_path("app/images/{$fileName}.zip");
            $zip = new ZipArchive();
            $images = $album->images;
            if ($images->isNotEmpty()) {
                if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                    $images->each(function ($item, $key) use (&$zip) {
                        $image = storage_path("app/" . $item["uri"]);
                        $zip->addFile($image, $item["name"]);
                    });
                    $zip->close();
                    return Storage::download("images/{$fileName}.zip", null, ['Content-Type' => "blob"]);
                }
            }
            return response()->json("the album doesn't contain any image", 404);
        }
    }
}
