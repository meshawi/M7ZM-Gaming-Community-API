<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Tag;
use App\Models\VideoTag;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class VideoUploadController extends Controller
{
    public function uploadVideo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m7zm_users,user_id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'required|file|mimes:mp4,mov,ogg,qt|max:512000',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg|max:4096',
            'visibility' => 'nullable|in:open,public,archived',
            'tags' => 'nullable|array',
            'tags.*' => 'integer|exists:tags,tag_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $videoFile = $request->file('video');
        $videoPath = $videoFile->store('public/videos');
        $videoFilename = basename($videoPath);

        $thumbnailPath = $request->file('thumbnail') ? $request->file('thumbnail')->store('public/video_thumbnails') : 'default_thumbnail.jpg';
        $thumbnailFilename = $thumbnailPath !== 'default_thumbnail.jpg' ? basename($thumbnailPath) : $thumbnailPath;

        $video = Video::create([
            'user_id' => $request->user_id,
            'title' => $request->title,
            'description' => $request->description,
            'video_path' => $videoFilename,
            'thumbnail_path' => $thumbnailFilename,
            'visibility' => $request->visibility ?? 'public',
        ]);

        if ($request->has('tags')) {
            $video->tags()->sync($request->tags);
        }

        return response()->json(['status' => 'success', 'message' => 'Video uploaded successfully.', 'video' => $video], 201);
    }

    
}
