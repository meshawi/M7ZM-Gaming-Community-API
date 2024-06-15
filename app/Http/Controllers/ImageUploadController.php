<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Image;
use App\Models\Tag;
use App\Models\ImageTag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class ImageUploadController extends Controller
{
    public function uploadImage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m7zm_users,user_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
            'visibility' => 'nullable|in:open,public,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,tag_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $imageFile = $request->file('image');
        $imagePath = $imageFile->store('public/images');
        $imageFilename = basename($imagePath);

        $image = Image::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'image_path' => $imageFilename,
            'visibility' => $request->visibility ?? 'public',
        ]);

        if ($request->has('tags')) {
            $image->tags()->sync($request->tags);
        }

        return response()->json(['status' => 'success', 'message' => 'Image uploaded successfully.', 'image' => $image], 201);
    }
}
