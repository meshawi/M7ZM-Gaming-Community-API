<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Game;

class GameController extends Controller
{
    public function getAllGames()
    {
        $games = Game::all();

        // Add full URL for the thumbnail
        foreach ($games as $game) {
            $game->thumbnail = asset('storage/game_thumbnails/' . $game->thumbnail);
        }

        return response()->json([
            'status' => 'success',
            'games' => $games
        ], 200);
    }
}
