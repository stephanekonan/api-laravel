<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'country' => ['required', 'string'],
                'phone' => ['required', 'regex:/^\d{10}$/'],
                'password' => ['required', 'string', 'min:6']
            ],
            [
                'username.required' => 'Le champ nom et prénoms est obligatoire.',
                'username.max' => 'Le champ nom et prénoms ne peut pas dépasser 255 caractères.',
                'email.unique' => 'Cette adresse email est déjà utilisée.',
                'phone.regex' => 'Le numéro de téléphone doit contenir exactement 10 chiffres.',
                'password.min' => 'Le mot de passe doit contenir au moins 6 caractères.'
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'country' => $request->country,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(40)
        ]);

        return response()->json([
            'message' => 'Inscription réussie',
            'user' => $user
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'email' => ['required', 'email'],
                'password' => ['required', 'string']
            ],
            [
                'email.required' => 'Le champ email est obligatoire.',
                'email.email' => 'Le champ email doit être valide.',
                'password.required' => 'Le mot de passe est obligatoire.',
                'password.string' => 'Le mot de passe doit être une chaîne de caractères.'
            ]
        );

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Aucun utilisateur trouvé avec cet email.'], 404);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Mot de passe incorrect.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => $user
        ]);
    }

    public function logout(Request $request)
    {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Utilisateur non authentifié'
            ], 401);
        }

        $tokens = $user->tokens;

        if (!$tokens) {
            return response()->json(['message' => 'Token introuvable']);
        }

        foreach ($tokens as $token) {
            $token->delete();
        }

        return response()->json([
            'message' => 'Déconnexion réussie'
        ], 200);
    }

    public function profil(Request $request)
    {

        $user = $request->user();

        if (!$user) {
            return response()->json([
                'error' => 'Utilisateur non authentifié'
            ], 401);
        }

        return response()->json([
            'user' => $user
        ], 200);
    }

    public function users()
    {
        $users = User::all();

        return response()->json(['users' => $users]);
    }
}
