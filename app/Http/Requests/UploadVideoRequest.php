<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadVideoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,avi,mkv|max:20480', // Max 20MB
            'thumbnail' => 'nullable|file|mimes:jpg,jpeg,png|max:2048', // Max 2MB
            'tags' => 'nullable|array',
            'tags.*' => 'nullable|string|distinct',
            'visibility' => 'required|in:open,public,archived'
        ];
    }
}
