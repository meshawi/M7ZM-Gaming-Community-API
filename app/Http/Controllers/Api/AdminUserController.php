<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M7ZMUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
class AdminUserController extends Controller
{
    public function editUserDetails(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'nullable|string|max:100|unique:m7zm_users,username,' . $user_id . ',user_id',
            'password' => 'nullable|string|min:6',
            'full_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|file|mimes:jpeg,png,jpg,gif|max:5120',
            'status' => 'nullable|in:active,inactive,banned',
            'profile_visibility' => 'nullable|in:public,private',
            'discord_role' => 'nullable|string|max:100',
            'user_prefer_url' => 'nullable|string|max:255|url',
            'authorization_level' => 'nullable|in:ADMIN,Moderator,User',
            'accounts_ids' => 'nullable|array',
            'login_history' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        if ($request->has('username')) {
            $user->username = $request->username;
        }
        if ($request->has('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
        }
        if ($request->has('bio')) {
            $user->bio = $request->bio;
        }
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if it's not the default one
            if ($user->profile_picture !== 'default_profile_picture.jpg') {
                Storage::delete('public/profile_pictures/' . $user->profile_picture);
            }
            $imageFile = $request->file('profile_picture');
            $imagePath = $imageFile->store('public/profile_pictures');
            $imageFilename = basename($imagePath);
            $user->profile_picture = $imageFilename;
        }
        if ($request->has('status')) {
            $user->status = $request->status;
        }
        if ($request->has('profile_visibility')) {
            $user->profile_visibility = $request->profile_visibility;
        }
        if ($request->has('discord_role')) {
            $user->discord_role = $request->discord_role;
        }
        if ($request->has('user_prefer_url')) {
            $user->user_prefer_url = $request->user_prefer_url;
        }
        if ($request->has('authorization_level')) {
            $user->authorization_level = $request->authorization_level;
        }
        if ($request->has('accounts_ids')) {
            $user->accounts_ids = $request->accounts_ids;
        }
        if ($request->has('login_history')) {
            $user->login_history = $request->login_history;
        }

        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User details updated successfully', 'user' => $user], 200);
    }
    public function deleteUser($user_id)
    {
        $user = M7ZMUser::find($user_id);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        // Delete the user's profile picture if it's not the default one
        if ($user->profile_picture !== 'default_profile_picture.jpg') {
            Storage::delete('public/profile_pictures/' . $user->profile_picture);
        }

        // Update related data to indicate the user has been deleted
        DB::table('videos')->where('user_id', $user_id)->update(['title' => DB::raw("CONCAT(title, ' (UPLOADED BY DELETED USER)')")]);
        DB::table('images')->where('user_id', $user_id)->update(['title' => DB::raw("CONCAT(title, ' (UPLOADED BY DELETED USER)')")]);
        DB::table('comments')->where('user_id', $user_id)->update(['comment_text' => DB::raw("CONCAT(comment_text, ' (COMMENTED BY DELETED USER)')")]);

        // Optionally, you can mark the user as deleted in your application instead of actually deleting the record
        $user->username = 'deleted_user_' . $user->user_id;
        $user->password = '';
        $user->full_name = 'Deleted User';
        $user->bio = '';
        $user->profile_picture = 'default_profile_picture.jpg';
        $user->status = 'inactive';
        $user->profile_visibility = 'private';
        $user->discord_role = null;
        $user->user_prefer_url = null;
        $user->authorization_level = 'User';
        $user->accounts_ids = json_encode([]);
        $user->login_history = json_encode([]);
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User deleted successfully'], 200);
    }
}
