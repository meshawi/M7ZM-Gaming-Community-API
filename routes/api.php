<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\CodController;
use App\Http\Controllers\Api\WorkshopController;
use App\Http\Controllers\Api\SDCardController;
use App\Http\Controllers\Api\GameInstructionsController;
use App\Http\Controllers\Api\M7ZMUserController;
use App\Http\Controllers\Api\MediaController;
use App\Http\Controllers\VideoUploadController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ImageUploadController;
use App\Http\Controllers\AllEditsController;
use App\Http\Controllers\VideoReactionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\Api\GameController;

// Register
Route::post('/register', [ApiController::class, 'register']);

// Login
Route::post('/login', [ApiController::class, 'login']);

Route::group(['middleware' => ['auth:sanctum']], function () {
  // Profile
  Route::get('/profile', [ApiController::class, 'profile']);

  // Logout
  Route::get('/logout', [ApiController::class, 'logout']);

  // Get COD games
  Route::get('/cod', [CodController::class, 'getCodGames']);

  Route::get('/workshop-games', [WorkshopController::class, 'getWorkshopGames']);

  Route::get('/sd-cards', [SDCardController::class, 'getSDCards']);

  Route::get('/sd-instructions', [GameInstructionsController::class, 'getGameInstructions']);
});

  // Get COD games
  Route::get('/cod', [CodController::class, 'getCodGames']);

  Route::get('/workshop-games', [WorkshopController::class, 'getWorkshopGames']);

  Route::get('/sd-cards', [SDCardController::class, 'getSDCards']);

  Route::get('/sd-instructions', [GameInstructionsController::class, 'getGameInstructions']);
// Get user details by username
Route::get('/m7zm_user/{username}', [App\Http\Controllers\Api\M7ZMUserController::class, 'getUserByUsername']);


// Get videos with visibility 'open' or 'public'
Route::get('/user/{username}/videos/open-public', [MediaController::class, 'getOpenOrPublicVideos']);

// Get images with visibility 'open' or 'public'
Route::get('/user/{username}/images/open-public', [MediaController::class, 'getOpenOrPublicImages']);

// Get all favorite videos for the user
Route::get('/user/{username}/favorite-videos', [MediaController::class, 'getFavoriteVideos']);

// Get all archived videos and images for the user
Route::get('/user/{username}/archived-media', [MediaController::class, 'getArchivedMedia']);

Route::group(['middleware' => ['auth:sanctum', 'guard:m7zm_user']], function () {
  Route::get('/profile', [ApiController::class, 'profile']);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('/m7zm-login', [M7ZMUserController::class, 'login']);
Route::post('/m7zm-register', [M7ZMUserController::class, 'register']);
Route::post('/upload-video', [VideoUploadController::class, 'uploadVideo']);
Route::get('/tags', [TagController::class, 'getAllTags']);
Route::post('/upload-image', [ImageUploadController::class, 'uploadImage']);
Route::put('/edit-video/{video_id}', [AllEditsController::class, 'editVideo']);
Route::get('/all-videos/{username}', [MediaController::class, 'getAllVideos']);
Route::get('/all-images/{username}', [MediaController::class, 'getAllImages']);
Route::put('/edit-image/{image_id}', [AllEditsController::class, 'editImage']);
Route::delete('/delete-image/{image_id}', [AllEditsController::class, 'deleteImage']);
Route::delete('/delete-video/{video_id}', [AllEditsController::class, 'deleteVideo']);
Route::get('/all-public-videos', [MediaController::class, 'getAllPublicVideos']);
Route::get('/all-public-images', [MediaController::class, 'getAllPublicImages']);
Route::get('/video-details/{video_id}', [MediaController::class, 'getVideoDetails']);
Route::post('/video/{video_id}/react', [VideoReactionController::class, 'reactToVideo']);
Route::post('/video/{video_id}/update-reaction', [VideoReactionController::class, 'updateReaction']);
Route::get('/video/{video_id}/reaction/{user_id}', [VideoReactionController::class, 'checkUserReaction']);
Route::post('/video/{video_id}/comment', [CommentController::class, 'addComment']);
Route::get('/video/{video_id}/comments', [CommentController::class, 'getComments']);
Route::put('/comment/{comment_id}', [CommentController::class, 'editComment']);
Route::delete('/comment/{comment_id}', [CommentController::class, 'deleteComment']);
Route::get('/video/{video_id}/favorite/{user_id}', [FavoriteController::class, 'checkFavorite']);
Route::post('/video/{video_id}/favorite', [FavoriteController::class, 'addFavorite']);
Route::delete('/video/{video_id}/favorite/{user_id}', [FavoriteController::class, 'removeFavorite']);
Route::put('/m7zm_user/update-username-password/{user_id}', [M7ZMUserController::class, 'updateUsernamePassword']);
Route::put('/m7zm_user/update-fullname-bio-visibility/{user_id}', [M7ZMUserController::class, 'updateFullnameBioVisibility']);
Route::post('/m7zm_user/update-profile-picture/{user_id}', [M7ZMUserController::class, 'updateProfilePicture']);
Route::put('/m7zm_user/update-account-ids/{user_id}', [M7ZMUserController::class, 'updateAccountIds']);
Route::put('/m7zm_user/update-achieved-games/{user_id}', [M7ZMUserController::class, 'updateAchievedGames']);
Route::put('/m7zm_user/update-favorite-games/{user_id}', [M7ZMUserController::class, 'updateFavoriteGames']);
Route::get('/games', [GameController::class, 'getAllGames']);
Route::get('/m7zm_users', [M7ZMUserController::class, 'getAllUsers']);

use App\Http\Controllers\Api\AdminUserController;

Route::put('/admin/edit-user/{user_id}', [AdminUserController::class, 'editUserDetails']);
Route::delete('/admin/delete-user/{user_id}', [AdminUserController::class, 'deleteUser']);

use App\Http\Controllers\Api\AdminMediaController;

Route::get('/admin/videos', [AdminMediaController::class, 'getAllVideos']);
Route::get('/admin/images', [AdminMediaController::class, 'getAllImages']);
