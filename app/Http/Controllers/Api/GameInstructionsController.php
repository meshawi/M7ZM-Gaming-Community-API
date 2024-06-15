<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GameInstructionsController extends Controller
{
    public function getGameInstructions()
    {
        try {
            $path = storage_path('app/public/GameInstructions.json');
            if (!file_exists($path)) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'File not found',
                    ],
                    404
                );
            }

            $json = file_get_contents($path);
            $data = json_decode($json, true);

            return response()->json(
                [
                    'status' => true,
                    'message' => 'Game Instructions data retrieved successfully',
                    'data' => $data,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'An error occurred while retrieving Game Instructions data',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }
}
