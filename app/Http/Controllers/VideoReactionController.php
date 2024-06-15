<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VideoReaction;
use Illuminate\Support\Facades\Validator;

class VideoReactionController extends Controller
{
    public function reactToVideo(Request $request, $video_id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m7zm_users,user_id',
            'reaction_type' => 'required|in:like,dislike'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user_id = $request->user_id;
        $reaction_type = $request->reaction_type;

        $existingReaction = VideoReaction::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->first();

        if ($existingReaction) {
            return response()->json(['status' => 'error', 'message' => 'Reaction already exists'], 400);
        }

        VideoReaction::create([
            'video_id' => $video_id,
            'user_id' => $user_id,
            'reaction_type' => $reaction_type
        ]);

        $likes = VideoReaction::where('video_id', $video_id)
            ->where('reaction_type', 'like')
            ->count();

        $dislikes = VideoReaction::where('video_id', $video_id)
            ->where('reaction_type', 'dislike')
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Reaction added successfully',
            'likes' => $likes,
            'dislikes' => $dislikes
        ], 201);
    }

    public function updateReaction(Request $request, $video_id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m7zm_users,user_id',
            'reaction_type' => 'required|in:like,dislike'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user_id = $request->user_id;
        $reaction_type = $request->reaction_type;

        $existingReaction = VideoReaction::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$existingReaction) {
            return response()->json(['status' => 'error', 'message' => 'Reaction not found'], 404);
        }

        $existingReaction->reaction_type = $reaction_type;
        $existingReaction->save();

        $likes = VideoReaction::where('video_id', $video_id)
            ->where('reaction_type', 'like')
            ->count();

        $dislikes = VideoReaction::where('video_id', $video_id)
            ->where('reaction_type', 'dislike')
            ->count();

        return response()->json([
            'status' => 'success',
            'message' => 'Reaction updated successfully',
            'likes' => $likes,
            'dislikes' => $dislikes
        ], 200);
    }
    public function checkUserReaction($video_id, $user_id)
    {
        $reaction = VideoReaction::where('video_id', $video_id)
            ->where('user_id', $user_id)
            ->first();

        if (!$reaction) {
            return response()->json([
                'status' => 'success',
                'message' => 'No reaction found for this user',
                'reaction' => null
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Reaction found',
            'reaction' => $reaction->reaction_type
        ], 200);
    }
}
