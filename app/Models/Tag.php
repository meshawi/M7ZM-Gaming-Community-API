<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['tag_name'];

    public function videos()
    {
        return $this->belongsToMany(Video::class, 'video_tags', 'tag_id', 'video_id');
    }
}
