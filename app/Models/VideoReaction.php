<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoReaction extends Model
{
    use HasFactory;

    protected $primaryKey = 'reaction_id';

    protected $fillable = [
        'video_id',
        'user_id',
        'reaction_type'
    ];

    public $timestamps = false;
}
