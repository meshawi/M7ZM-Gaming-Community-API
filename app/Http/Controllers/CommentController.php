<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Validator;
use App\Models\M7ZMUser;

class CommentController extends Controller
{
    public function addComment(Request $request, $video_id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer|exists:m7zm_users,user_id',
            'comment_text' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $comment = Comment::create([
            'video_id' => $video_id,
            'user_id' => $request->user_id,
            'comment_text' => $request->comment_text
        ]);

        $user = M7ZMUser::find($request->user_id);
        $profile_picture = asset('storage/profile_pictures/' . $user->profile_picture);

        return response()->json([
            'status' => 'success',
            'message' => 'Comment added successfully',
            'comment' => [
                'video_id' => $comment->video_id,
                'user_id' => $comment->user_id,
                'comment_text' => $comment->comment_text,
                'comment_id' => $comment->comment_id,
                'profile_picture' => $profile_picture
            ]
        ], 201);
    }

    public function getComments($video_id)
    {
        $comments = Comment::where('video_id', $video_id)
            ->join('m7zm_users', 'comments.user_id', '=', 'm7zm_users.user_id')
            ->select('comments.*', 'm7zm_users.username', 'm7zm_users.profile_picture')
            ->get();

        foreach ($comments as $comment) {
            $comment->profile_picture = asset('storage/profile_pictures/' . $comment->profile_picture);
        }

        return response()->json(['status' => 'success', 'comments' => $comments], 200);
    }

    public function editComment(Request $request, $comment_id)
    {
        $validator = Validator::make($request->all(), [
            'comment_text' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $comment = Comment::find($comment_id);
        if (!$comment) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found'], 404);
        }

        $comment->comment_text = $request->comment_text;
        $comment->save();

        return response()->json(['status' => 'success', 'message' => 'Comment updated successfully', 'comment' => $comment], 200);
    }

    public function deleteComment($comment_id)
    {
        $comment = Comment::find($comment_id);
        if (!$comment) {
            return response()->json(['status' => 'error', 'message' => 'Comment not found'], 404);
        }

        $comment->delete();

        return response()->json(['status' => 'success', 'message' => 'Comment deleted successfully'], 200);
    }
}
