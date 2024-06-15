<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Video;
use App\Models\Image;
use App\Models\M7ZMUser;

class AdminMediaController extends Controller
{
    public function getAllVideos()
    {
        $videos = Video::with('user')->get();

        foreach ($videos as $video) {
            $video->video_path = asset('storage/videos/' . $video->video_path);
            $video->thumbnail_path = asset('storage/video_thumbnails/' . $video->thumbnail_path);

            if ($video->user) {
                $video->user->profile_picture = asset('storage/profile_pictures/' . $video->user->profile_picture);
            } else {
                $video->user = (object) [
                    'username' => 'Deleted User',
                    'profile_picture' => asset('storage/profile_pictures/default_profile_picture.jpg')
                ];
            }
        }

        return response()->json(['status' => 'success', 'videos' => $videos], 200);
    }

    public function getAllImages()
    {
        $images = Image::with('user')->get();

        foreach ($images as $image) {
            $image->image_path = asset('storage/images/' . $image->image_path);

            if ($image->user) {
                $image->user->profile_picture = asset('storage/profile_pictures/' . $image->user->profile_picture);
            } else {
                $image->user = (object) [
                    'username' => 'Deleted User',
                    'profile_picture' => asset('storage/profile_pictures/default_profile_picture.jpg')
                ];
            }
        }

        return response()->json(['status' => 'success', 'images' => $images], 200);
    }
}
