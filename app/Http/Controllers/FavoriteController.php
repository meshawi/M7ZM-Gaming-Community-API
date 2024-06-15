<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFavoriteVideo;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function checkFavorite($video_id, $user_id)
    {
        $favorite = UserFavoriteVideo::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$favorite) {
            return response()->json([
                'status' => 'success',
                'message' => 'Video not in favorites',
                'favorite' => false
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Video is in favorites',
            'favorite' => true
        ], 200);
    }

    public function addFavorite(Request $request, $video_id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m7zm_users,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user_id = $request->user_id;

        $existingFavorite = UserFavoriteVideo::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->first();

        if ($existingFavorite) {
            return response()->json(['status' => 'error', 'message' => 'Video already in favorites'], 400);
        }

        UserFavoriteVideo::create([
            'video_id' => $video_id,
            'user_id' => $user_id
        ]);

        return response()->json(['status' => 'success', 'message' => 'Video added to favorites'], 201);
    }

    public function removeFavorite($video_id, $user_id)
    {
        $favorite = UserFavoriteVideo::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$favorite) {
            return response()->json(['status' => 'error', 'message' => 'Video not found in favorites'], 404);
        }

        UserFavoriteVideo::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->delete();

        return response()->json(['status' => 'success', 'message' => 'Video removed from favorites'], 200);
    }
}
