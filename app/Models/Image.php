<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $primaryKey = 'image_id';

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_path',
        'visibility'
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'image_tags', 'image_id', 'tag_id');
    }

    public function user()
    {
        return $this->belongsTo(M7ZMUser::class, 'user_id');
    }
}
