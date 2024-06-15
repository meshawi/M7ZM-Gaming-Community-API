<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImageTag extends Model
{
    use HasFactory;

    protected $table = 'image_tags';

    protected $fillable = ['image_id', 'tag_id'];
}
