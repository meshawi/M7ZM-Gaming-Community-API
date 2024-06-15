<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ImageTag; // Add this import
use App\Models\VideoTag; // Add this import

class AllEditsController extends Controller
{
    public function editVideo(Request $request, $video_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'visibility' => 'sometimes|nullable|in:open,public,archived',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'integer|exists:tags,tag_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $video = Video::find($video_id);
        if (!$video) {
            return response()->json(['status' => 'error', 'message' => 'Video not found.'], 404);
        }

        if ($request->has('title')) {
            $video->title = $request->title;
        }

        if ($request->has('description')) {
            $video->description = $request->description;
        }

        if ($request->has('visibility')) {
            $video->visibility = $request->visibility;
        }

        $video->save();

        if ($request->has('tags')) {
            $video->tags()->sync($request->tags);
        }

        return response()->json(['status' => 'success', 'message' => 'Video updated successfully.', 'video' => $video], 200);
    }

    public function editImage(Request $request, $image_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'visibility' => 'sometimes|nullable|in:open,public,archived',
            'tags' => 'sometimes|nullable|array',
            'tags.*' => 'integer|exists:tags,tag_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $image = Image::find($image_id);
        if (!$image) {
            return response()->json(['status' => 'error', 'message' => 'Image not found.'], 404);
        }

        if ($request->has('title')) {
            $image->title = $request->title;
        }

        if ($request->has('description')) {
            $image->description = $request->description;
        }

        if ($request->has('visibility')) {
            $image->visibility = $request->visibility;
        }

        $image->save();

        if ($request->has('tags')) {
            $image->tags()->sync($request->tags);
        }

        return response()->json(['status' => 'success', 'message' => 'Image updated successfully.', 'image' => $image], 200);
    }
    public function deleteImage($image_id)
    {
        $image = Image::find($image_id);
        if (!$image) {
            return response()->json(['status' => 'error', 'message' => 'Image not found.'], 404);
        }

        // Delete the related tags from image_tags
        ImageTag::where('image_id', $image_id)->delete();

        // Delete the image file from storage
        if (Storage::exists('public/images/' . $image->image_path)) {
            Storage::delete('public/images/' . $image->image_path);
        }

        // Delete the image record from the database
        $image->delete();

        return response()->json(['status' => 'success', 'message' => 'Image deleted successfully.'], 200);
    }
    public function deleteVideo($video_id)
    {
        $video = Video::find($video_id);
        if (!$video) {
            return response()->json(['status' => 'error', 'message' => 'Video not found.'], 404);
        }

        // Delete the related tags from video_tags
        VideoTag::where('video_id', $video_id)->delete();

        // Delete the video file from storage
        if (Storage::exists('public/videos/' . $video->video_path)) {
            Storage::delete('public/videos/' . $video->video_path);
        }

        // Delete the thumbnail file from storage
        if (Storage::exists('public/video_thumbnails/' . $video->thumbnail_path)) {
            Storage::delete('public/video_thumbnails/' . $video->thumbnail_path);
        }

        // Delete the video record from the database
        $video->delete();

        return response()->json(['status' => 'success', 'message' => 'Video deleted successfully.'], 200);
    }
}
