<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class M7ZMUser extends Authenticatable
{
    use HasApiTokens, HasFactory;

    protected $table = 'M7ZM_users';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'username',
        'password',
        'full_name',
        'bio',
        'profile_picture',
        'status',
        'profile_visibility',
        'discord_role',
        'user_prefer_url',
        'authorization_level',
        'accounts_ids',
        'login_history',
    ];

    protected $casts = [
        'accounts_ids' => 'array',
        'login_history' => 'array',
    ];

    protected $hidden = [
        'password',
    ];
}
