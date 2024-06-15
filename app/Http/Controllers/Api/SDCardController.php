<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SDCardController extends Controller
{
    public function getSDCards()
    {
        try {
            $path = storage_path('app/public/SDCards.json');
            if (!file_exists($path)) {
                return response()->json(
                    [
                        'status' => false,
                        'message' => 'File not found',
                    ],
                    404
                );
            }

            $data = json_decode(file_get_contents($path), true);

            // Modify image paths to include the full URL
            foreach ($data as &$deta) {
                $deta['cardImage'] = url($deta['cardImage']);
            }


            return response()->json(
                [
                    'status' => true,
                    'message' => 'SD Cards data retrieved successfully',
                    'data' => $data,
                ],
                200
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => false,
                    'message' => 'An error occurred while retrieving SD Cards data',
                    'error' => $e->getMessage(),
                ],
                500
            );
        }
    }
}
