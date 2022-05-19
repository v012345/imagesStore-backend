<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Album;
use Illuminate\Http\Request;

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
            // $query->limit(1);
        }])->withCount("images")->orderByDesc("id")->toSql();
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
}
