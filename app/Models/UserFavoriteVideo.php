<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFavoriteVideo extends Model
{
    use HasFactory;

    protected $table = 'user_favorite_videos';

    protected $fillable = [
        'video_id',
        'user_id'
    ];

    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'int';
}
