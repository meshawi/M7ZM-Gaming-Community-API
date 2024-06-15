<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{
    public function getAllTags()
    {
        $tags = Tag::all();
        return response()->json(['status' => 'success', 'tags' => $tags], 200);
    }
}
