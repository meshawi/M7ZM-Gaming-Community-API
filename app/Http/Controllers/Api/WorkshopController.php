<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class WorkshopController extends Controller
{
    public function getWorkshopGames()
    {
        try {
            $path = storage_path('app/public/workshop.json');
            if (!File::exists($path)) {
                return response()->json([
                    'status' => false,
                    'message' => 'file_not_found'
                ], 404);
            }

            $fileContents = File::get($path);
            $workshopGames = json_decode($fileContents, true);

            return response()->json([
                'status' => true,
                'message' => 'workshop_games_retrieved',
                'data' => $workshopGames
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage(),
            ], 500);
        }
    }
}
