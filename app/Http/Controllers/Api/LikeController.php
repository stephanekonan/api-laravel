<?php

namespace App\Http\Controllers\Api;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class LikeController extends Controller
{
    public function like(Request $request)
    {
        $validatedData = $request->validate([
            'likeable_id' => 'required|integer',
            'likeable_type' => 'required|string',
        ]);

        $userId = Auth::id();

        $existingLike = Like::where('user_id', $userId)
            ->where('likeable_id', $validatedData['likeable_id'])
            ->where('likeable_type', $validatedData['likeable_type'])
            ->first();

        if ($existingLike) {
            $existingLike->delete();
            return response()->json(['message' => 'Like supprimé.'], 200);
        }

        $like = Like::create([
            'user_id' => $userId,
            'likeable_id' => $validatedData['likeable_id'],
            'likeable_type' => $validatedData['likeable_type'],
        ]);

        return response()->json($like, 201);
    }
}
