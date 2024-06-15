<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\M7ZMUser;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class M7ZMUserController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:100|unique:m7zm_users',
            'password' => 'required|string|min:6',
            'full_name' => 'required|string|max:255',
            'bio' => 'nullable|string',
            'profile_picture' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,inactive,banned',
            'profile_visibility' => 'nullable|in:public,private',
            'discord_role' => 'nullable|string|max:100',
            'user_prefer_url' => 'nullable|string|max:255',
            'authorization_level' => 'nullable|in:ADMIN,Moderator,User',
            'accounts_ids' => 'nullable|array',
            'login_history' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = M7ZMUser::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'full_name' => $request->full_name,
            'bio' => $request->bio,
            'profile_picture' => $request->profile_picture ?? 'default_profile_picture.jpg',
            'status' => $request->status ?? 'active',
            'profile_visibility' => $request->profile_visibility ?? 'public',
            'discord_role' => $request->discord_role,
            'user_prefer_url' => $request->user_prefer_url,
            'authorization_level' => $request->authorization_level ?? 'User',
            'accounts_ids' => $request->accounts_ids ?? [],
            'login_history' => $request->login_history ?? [],
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully.',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = M7ZMUser::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid username or password.'
            ], 401);
        }

        // Add current timestamp to login_history
        $loginHistory = $user->login_history;
        $loginHistory[] = date('Y-m-d H:i:s');
        $user->login_history = $loginHistory;
        $user->last_login = date('Y-m-d H:i:s');
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Login successful.',
            'user_id' => $user->user_id,
            'username' => $user->username,
            'authorization_level' => $user->authorization_level,
            'user_status' => $user->status,
        ], 200);
    }

    public function getUserByUsername($username)
    {
        $user = M7ZMUser::where('username', $username)->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'User not found',
                'data' => null
            ], 200); // Returning 200 status for UI to handle gracefully
        }

        // Retrieve favorite games
        $favoriteGames = DB::table('user_favorite_games')
            ->join('games', 'user_favorite_games.game_id', '=', 'games.game_id')
            ->where('user_favorite_games.user_id', $user->user_id)
            ->orderBy('user_favorite_games.rank')
            ->select('games.game_id', 'games.game_name', 'games.game_details', 'games.thumbnail', 'user_favorite_games.rank')
            ->get();

        // Add full URL for the thumbnail
        foreach ($favoriteGames as $game) {
            $game->thumbnail = asset('storage/game_thumbnails/' . $game->thumbnail);
        }

        // Retrieve achieved games
        $achievedGames = DB::table('user_games_achieved')
            ->join('games', 'user_games_achieved.game_id', '=', 'games.game_id')
            ->where('user_games_achieved.user_id', $user->user_id)
            ->select('games.game_id', 'games.game_name', 'games.game_details', 'games.thumbnail')
            ->get();

        // Add full URL for the thumbnail
        foreach ($achievedGames as $game) {
            $game->thumbnail = asset('storage/game_thumbnails/' . $game->thumbnail);
        }

        // Count number of video uploads
        $videoUploadCount = DB::table('videos')
            ->where('user_id', $user->user_id)
            ->count();

        // Count number of image uploads
        $imageUploadCount = DB::table('images')
            ->where('user_id', $user->user_id)
            ->count();

        // Count number of likes received on user's videos
        $videoLikeCount = DB::table('video_reactions')
            ->join('videos', 'video_reactions.video_id', '=', 'videos.video_id')
            ->where('videos.user_id', $user->user_id)
            ->where('video_reactions.reaction_type', 'like')
            ->count();

        // Count number of likes received on user's images
        $imageLikeCount = DB::table('image_reactions')
            ->join('images', 'image_reactions.image_id', '=', 'images.image_id')
            ->where('images.user_id', $user->user_id)
            ->where('image_reactions.reaction_type', 'like')
            ->count();

        // Decode the accounts_ids JSON field if it's a string
        $accountsIds = is_string($user->accounts_ids) ? json_decode($user->accounts_ids, true) : $user->accounts_ids;

        return response()->json([
            'status' => true,
            'message' => 'User details retrieved successfully',
            'data' => [
                'username' => $user->username,
                'full_name' => $user->full_name,
                'bio' => $user->bio,
                'profile_picture' => asset('storage/profile_pictures/' . $user->profile_picture),
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'last_login' => $user->last_login,
                'status' => $user->status,
                'profile_visibility' => $user->profile_visibility,
                'discord_role' => $user->discord_role,
                'user_prefer_url' => $user->user_prefer_url,
                'authorization_level' => $user->authorization_level,
                'accounts_ids' => $accountsIds,
                'login_history' => $user->login_history,
                'favorite_games' => $favoriteGames,
                'achieved_games' => $achievedGames,
                'uploads_count' => $videoUploadCount + $imageUploadCount,
                'likes_received_count' => $videoLikeCount + $imageLikeCount
            ]
        ], 200);
    }

    public function updateUsernamePassword(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'nullable|string|max:100|unique:m7zm_users',
            'password' => 'nullable|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        if ($request->username) {
            $user->username = $request->username;
        }
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User details updated successfully'], 200);
    }

    public function updateFullnameBioVisibility(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'nullable|string|max:255',
            'bio' => 'nullable|string',
            'profile_visibility' => 'nullable|in:public,private',
            'user_prefer_url' => 'nullable|string|max:255|url'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        if ($request->full_name) {
            $user->full_name = $request->full_name;
        }
        if ($request->bio) {
            $user->bio = $request->bio;
        }
        if ($request->profile_visibility) {
            $user->profile_visibility = $request->profile_visibility;
        }
        if ($request->user_prefer_url) {
            $user->user_prefer_url = $request->user_prefer_url;
        }

        $user->save();

        return response()->json(['status' => 'success', 'message' => 'User details updated successfully'], 200);
    }
    public function updateProfilePicture(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'profile_picture' => 'required|file|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
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

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile picture updated successfully',
            'profile_picture' => asset('storage/profile_pictures/' . $user->profile_picture)
        ], 200);
    }
    public function updateAccountIds(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'accounts_ids' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        $user->accounts_ids = $request->accounts_ids;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Account IDs updated successfully'], 200);
    }

    public function updateAchievedGames(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'achieved_games' => 'required|array',
            'achieved_games.*' => 'integer|exists:games,game_id',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        DB::table('user_games_achieved')->where('user_id', $user_id)->delete();
        foreach ($request->achieved_games as $game_id) {
            DB::table('user_games_achieved')->insert([
                'user_id' => $user_id,
                'game_id' => $game_id,
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Achieved games updated successfully'], 200);
    }

    public function updateFavoriteGames(Request $request, $user_id)
    {
        $validator = Validator::make($request->all(), [
            'favorite_games' => 'required|array',
            'favorite_games.*.game_id' => 'required|integer|exists:games,game_id',
            'favorite_games.*.rank' => 'required|integer|min:1|max:3',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        $user = M7ZMUser::find($user_id);
        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found'], 404);
        }

        DB::table('user_favorite_games')->where('user_id', $user_id)->delete();
        foreach ($request->favorite_games as $favorite_game) {
            DB::table('user_favorite_games')->insert([
                'user_id' => $user_id,
                'game_id' => $favorite_game['game_id'],
                'rank' => $favorite_game['rank'],
            ]);
        }

        return response()->json(['status' => 'success', 'message' => 'Favorite games updated successfully'], 200);
    }
    public function getAllUsers()
    {
        $users = M7ZMUser::all();

        // Add full URL for the profile picture
        foreach ($users as $user) {
            $user->profile_picture = asset('storage/profile_pictures/' . $user->profile_picture);
        }

        return response()->json([
            'status' => 'success',
            'users' => $users
        ], 200);
    }


}