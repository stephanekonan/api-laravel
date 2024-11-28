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
            'likeable_type' => 'required|string|in:Post,Event',
        ]);

        $userId = Auth::id();
        $likeableClass = "App\\Models\\" . $validatedData['likeable_type'];
        $likeable = $likeableClass::find($validatedData['likeable_id']);

        if (!$likeable) {
            return response()->json(['message' => 'Ressource introuvable'], 404);
        }

        $this->authorize('like', $likeable);

        $existingLike = Like::where('user_id', $userId)
            ->where('likeable_id', $validatedData['likeable_id'])
            ->where('likeable_type', $validatedData['likeable_type'])
            ->first();

        if ($existingLike) {

            $this->authorize('unlike', $likeable);

            $existingLike->delete();
            return response()->json([
                'message' => 'Like supprimé.',
                'liked' => false,
            ], 200);
        }

        Like::create([
            'user_id' => $userId,
            'likeable_id' => $validatedData['likeable_id'],
            'likeable_type' => $validatedData['likeable_type'],
        ]);

        return response()->json([
            'message' => 'Like ajouté.',
            'liked' => true,
        ], 201);
    }

    public function count($likeableId, $likeableType)
    {
        $likeableClass = "App\\Models\\" . ucfirst($likeableType);

        if (!in_array($likeableClass, ['App\\Models\\Post', 'App\\Models\\Event'])) {
            return response()->json(['message' => 'Type de ressource invalide'], 400);
        }

        $likeable = $likeableClass::find($likeableId);

        if (!$likeable) {
            return response()->json(['message' => 'Ressource introuvable'], 404);
        }

        return response()->json(['likes_count' => $likeable->likes()->count()], 200);
    }
}

