<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasFactory;

    protected $primaryKey = 'video_id';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'video_path',
        'thumbnail_path',
        'visibility'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'video_tags', 'video_id', 'tag_id');
    }

    public function user()
    {
        return $this->belongsTo(M7ZMUser::class, 'user_id');
    }
}
