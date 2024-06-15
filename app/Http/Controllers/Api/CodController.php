<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodController extends Controller
{
    public function getCodGames()
    {
        try {
            $path = storage_path('app/public/cod.json');

            if (!file_exists($path)) {
                return response()->json([
                    'status' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $codGames = json_decode(file_get_contents($path), true);

            // Modify image paths to include the full URL
            foreach ($codGames as &$game) {
                $game['image'] = url($game['image']);
            }

            return response()->json([
                'status' => true,
                'message' => 'COD games retrieved successfully',
                'data' => $codGames
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}