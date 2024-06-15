<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M7ZMUser;
use DB;

class MediaController extends Controller
{
    // Get videos with visibility 'open' or 'public'
    public function getOpenOrPublicVideos($username)
    {
        $user = M7ZMUser::where('username', $username)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $videos = DB::table('videos')
            ->where('user_id', $user->user_id)
            ->whereIn('visibility', ['open', 'public'])
            ->get();

        foreach ($videos as $video) {
            $video->video_path = asset('storage/videos/' . $video->video_path);
            $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);

            // Get tags for each video
            $tags = DB::table('video_tags')
                ->join('tags', 'video_tags.tag_id', '=', 'tags.tag_id')
                ->where('video_tags.video_id', $video->video_id)
                ->pluck('tags.tag_name');

            $video->tags = $tags;
        }

        if ($videos->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No videos found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $videos], 200);
    }

    // Get images with visibility 'open' or 'public'
    public function getOpenOrPublicImages($username)
    {
        $user = M7ZMUser::where('username', $username)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $images = DB::table('images')
            ->where('user_id', $user->user_id)
            ->whereIn('visibility', ['open', 'public'])
            ->get();

        foreach ($images as $image) {
            $image->image_path = asset('storage/images/' . $image->image_path);

            // Get tags for each image
            $tags = DB::table('image_tags')
                ->join('tags', 'image_tags.tag_id', '=', 'tags.tag_id')
                ->where('image_tags.image_id', $image->image_id)
                ->pluck('tags.tag_name');

            $image->tags = $tags;
        }

        if ($images->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No images found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $images], 200);
    }

    // Get all favorite videos for the user
    public function getFavoriteVideos($username)
    {
        $user = M7ZMUser::where('username', $username)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $favoriteVideos = DB::table('user_favorite_videos')
            ->join('videos', 'user_favorite_videos.video_id', '=', 'videos.video_id')
            ->where('user_favorite_videos.user_id', $user->user_id)
            ->get();

        foreach ($favoriteVideos as $video) {
            $video->video_path = asset('storage/videos/' . $video->video_path);
            $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);

            // Get tags for each video
            $tags = DB::table('video_tags')
                ->join('tags', 'video_tags.tag_id', '=', 'tags.tag_id')
                ->where('video_tags.video_id', $video->video_id)
                ->pluck('tags.tag_name');

            $video->tags = $tags;
        }

        if ($favoriteVideos->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No favorite videos found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $favoriteVideos], 200);
    }

    // Get all archived videos and images for the user
    public function getArchivedMedia($username)
    {
        $user = M7ZMUser::where('username', $username)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $archivedVideos = DB::table('videos')
            ->where('user_id', $user->user_id)
            ->where('visibility', 'archived')
            ->get();

        $archivedImages = DB::table('images')
            ->where('user_id', $user->user_id)
            ->where('visibility', 'archived')
            ->get();

        foreach ($archivedVideos as $video) {
            $video->video_path = asset('storage/videos/' . $video->video_path);
            $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);

            // Get tags for each video
            $tags = DB::table('video_tags')
                ->join('tags', 'video_tags.tag_id', '=', 'tags.tag_id')
                ->where('video_tags.video_id', $video->video_id)
                ->pluck('tags.tag_name');

            $video->tags = $tags;
        }

        foreach ($archivedImages as $image) {
            $image->image_path = asset('storage/images/' . $image->image_path);

            // Get tags for each image
            $tags = DB::table('image_tags')
                ->join('tags', 'image_tags.tag_id', '=', 'tags.tag_id')
                ->where('image_tags.image_id', $image->image_id)
                ->pluck('tags.tag_name');

            $image->tags = $tags;
        }

        if ($archivedVideos->isEmpty() && $archivedImages->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No archived media found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => ['videos' => $archivedVideos, 'images' => $archivedImages]], 200);
    }


    // Get all videos by username regardless of their visibility
    public function getAllVideos($username)
    {
        $user = M7ZMUser::where('username', $username)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $videos = DB::table('videos')
            ->where('user_id', $user->user_id)
            ->get();

        foreach ($videos as $video) {
            $video->video_path = asset('storage/videos/' . $video->video_path);
            $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);

            // Get tags for each video
            $tags = DB::table('video_tags')
                ->join('tags', 'video_tags.tag_id', '=', 'tags.tag_id')
                ->where('video_tags.video_id', $video->video_id)
                ->pluck('tags.tag_name');

            $video->tags = $tags;
        }

        if ($videos->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No videos found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $videos], 200);
    }
    // Get all images by username regardless of their visibility
    public function getAllImages($username)
    {
        $user = M7ZMUser::where('username', $username)->first();
        if (!$user) {
            return response()->json(['status' => false, 'message' => 'User not found'], 404);
        }

        $images = DB::table('images')
            ->where('user_id', $user->user_id)
            ->get();

        foreach ($images as $image) {
            $image->image_path = asset('storage/images/' . $image->image_path);

            // Get tags for each image
            $tags = DB::table('image_tags')
                ->join('tags', 'image_tags.tag_id', '=', 'tags.tag_id')
                ->where('image_tags.image_id', $image->image_id)
                ->pluck('tags.tag_name');

            $image->tags = $tags;
        }

        if ($images->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No images found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $images], 200);
    }

    public function getAllPublicVideos()
    {
        $videos = DB::table('videos')
            ->join('m7zm_users', 'videos.user_id', '=', 'm7zm_users.user_id')
            ->whereIn('visibility', ['open', 'public'])
            ->select('videos.*', 'm7zm_users.username', 'm7zm_users.user_id', 'm7zm_users.profile_picture')
            ->get();

        foreach ($videos as $video) {
            $video->video_path = asset('storage/videos/' . $video->video_path);
            $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);
            $video->profile_picture = asset('storage/profile_pictures/' . $video->profile_picture);

            // Get tags for each video
            $tags = DB::table('video_tags')
                ->join('tags', 'video_tags.tag_id', '=', 'tags.tag_id')
                ->where('video_tags.video_id', $video->video_id)
                ->pluck('tags.tag_name');

            $video->tags = $tags;
        }

        if ($videos->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No public or open videos found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $videos], 200);
    }

    public function getAllPublicImages()
    {
        $images = DB::table('images')
            ->join('m7zm_users', 'images.user_id', '=', 'm7zm_users.user_id')
            ->whereIn('visibility', ['open', 'public'])
            ->select('images.*', 'm7zm_users.username', 'm7zm_users.user_id', 'm7zm_users.profile_picture')
            ->get();

        foreach ($images as $image) {
            $image->image_path = asset('storage/images/' . $image->image_path);
            $image->profile_picture = asset('storage/profile_pictures/' . $image->profile_picture);

            // Get tags for each image
            $tags = DB::table('image_tags')
                ->join('tags', 'image_tags.tag_id', '=', 'tags.tag_id')
                ->where('image_tags.image_id', $image->image_id)
                ->pluck('tags.tag_name');
            
            $image->tags = $tags;
        }

        if ($images->isEmpty()) {
            return response()->json(['status' => true, 'message' => 'No public or open images found', 'data' => []], 200);
        }

        return response()->json(['status' => true, 'data' => $images], 200);
    }
     // Get video details by video_id
     public function getVideoDetails($video_id)
     {
         $video = DB::table('videos')
             ->join('m7zm_users', 'videos.user_id', '=', 'm7zm_users.user_id')
             ->where('videos.video_id', $video_id)
             ->select(
                 'videos.*',
                 'm7zm_users.username',
                 'm7zm_users.profile_picture'
             )
             ->first();
 
         if (!$video) {
             return response()->json(['status' => false, 'message' => 'Video not found'], 404);
         }
 
         // Format paths
         $video->video_path = asset('storage/videos/' . $video->video_path);
         $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);
         $video->profile_picture = asset('storage/profile_pictures/' . $video->profile_picture);
 
         // Get tags for the video
         $tags = DB::table('video_tags')
             ->join('tags', 'video_tags.tag_id', '=', 'tags.tag_id')
             ->where('video_tags.video_id', $video_id)
             ->pluck('tags.tag_name');
         $video->tags = $tags;
 
         // Get likes and dislikes
         $likes = DB::table('video_reactions')
             ->where('video_id', $video_id)
             ->where('reaction_type', 'like')
             ->count();
         $dislikes = DB::table('video_reactions')
             ->where('video_id', $video_id)
             ->where('reaction_type', 'dislike')
             ->count();
         $video->likes = $likes;
         $video->dislikes = $dislikes;
 
         // Get comments
         $comments = DB::table('comments')
             ->join('m7zm_users', 'comments.user_id', '=', 'm7zm_users.user_id')
             ->where('comments.video_id', $video_id)
             ->select('comments.*', 'm7zm_users.username', 'm7zm_users.profile_picture')
             ->get();
 
         foreach ($comments as $comment) {
             $comment->profile_picture = asset('storage/profile_pictures/' . $comment->profile_picture);
         }
 
         $video->comments = $comments;
 
         return response()->json(['status' => true, 'data' => $video], 200);
     }
     
     
}
