<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use OSS\OssClient;

class UploadImage implements ShouldQueue
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
    public function handle(OssClient $oss)
    {
        //
        $bucket = "market4scar";
        $oss->uploadFile($bucket, ($this->image)["object"], ($this->image)["path"]);
        unlink(($this->image)["path"]);
    }
}
