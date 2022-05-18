<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use App\Jobs\UploadImage;

class CreateThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $image;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($image)
    {
        //
        $this->image = $image;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $canvas = Image::canvas(300, 200);
        $thumbnail =  Image::make($this->image["path"]);
        $thumbnail->resize(300, 200, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $canvas->insert($thumbnail, 'center');
        $temp_file = storage_path("app/public/temp/" . $this->image["object"]);
        $canvas->save($temp_file);
        $canvas->destroy();
        $thumbnail->destroy();
        UploadImage::dispatch([
            "object" => $this->image["object"],
            "path" => $temp_file
        ]);
        unlink($temp_file);
        unlink($this->image["path"]);
    }
}
