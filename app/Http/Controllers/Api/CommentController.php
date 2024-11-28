<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with('user', 'post')->latest()->get();
        return response()->json([
            'message' => 'Tous les commentaires',
            'data' => $comments
        ]);
    }

    public function show($id)
    {
        $comment = Comment::with('user', 'post')->find($id);

        if (!$comment) {
            return response()->json(['message' => 'Commentaire introuvable'], 401);
        }

        return response()->json([
            'message' => 'Commentaire trouvé',
            'data' => $comment
        ], 200);
    }

    public function store(Request $request)
    {

        $this->authorize('create', Comment::class);

        $validatedData = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string|min:5|max:500',
        ], [
            'post_id.required' => 'Le champ post_id est obligatoire.',
            'post_id.exists' => 'Le post spécifié n\'existe pas.',
            'content.required' => 'Le champ contenu est obligatoire.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.',
            'content.min' => 'Le contenu doit contenir au moins 5 caractères.',
            'content.max' => 'Le contenu ne doit pas dépasser 500 caractères.',
        ]);

        $comment = Comment::create($validatedData);

        return response()->json($comment, 201);
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        $this->authorize('update', $comment);

        $validatedData = $request->validate([
            'content' => 'required|string|min:5|max:500',
        ], [
            'content.required' => 'Le contenu est obligatoire.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.',
            'content.min' => 'Le contenu doit contenir au moins 5 caractères.',
            'content.max' => 'Le contenu ne doit pas dépasser 500 caractères.',
        ]);

        $comment->update([
            'content' => $validatedData['content'],
        ]);

        return response()->json($comment, 200);
    }

    public function destroy($id)
    {
        $comment = Comment::find($id);

        $this->authorize('delete', $comment);

        if (!$comment) {
            return response()->json(['message' => 'Commentaire non trouvé.'], 404);
        }

        $post = Post::find($comment->post_id);

        if (!$post) {
            return response()->json(['message' => 'Le post associé au commentaire n\'existe pas.'], 404);
        }

        $comment->delete();

        return response()->json(['message' => 'Commentaire supprimé avec succès.'], 200);
    }
}
