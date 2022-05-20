<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Image extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'size',
        'type',
        'width',
        'height',
        'uri',
        'thumbnail_uri',
    ];



    public function albums()
    {
        return $this->belongsToMany(Album::class);
    }
    // protected $dates  = ['created_at', 'updated_at'];
    // protected $casts = [
    //     'created_at' => 'datetime',
    //     'updated_at' => 'datetime',
    // ];
    public function getCreatedAtAttribute($date)
    {
        return Carbon::parse($date)->setTimezone("Asia/ShangHai")->toDateTimeString();
    }

    public function getUpdatedAtAttribute($date)
    {
        return Carbon::parse($date)->setTimezone("Asia/ShangHai")->toDateTimeString();
    }
}
