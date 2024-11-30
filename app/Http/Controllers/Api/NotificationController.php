<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class NotificationController extends Controller
{
    public function saveFcmToken(Request $request)
    {
        $request->validate([
            'fcm_token' => 'required|string',
        ]);

        $userCurrent = auth()->user();
        $userData = User::where('id', $userCurrent->id);

        $userData->fcm_token = $request->fcm_token;
        $userData->save();

        return response()->json([
            'message' => 'Token FCM enregistré avec succès',
        ], 200);
    }
}
