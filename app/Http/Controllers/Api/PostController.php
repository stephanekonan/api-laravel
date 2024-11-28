<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('is_feeded', false)->latest()->get();
        return response()->json($posts, 200);
    }

    public function feeded()
    {
        $feededPosts = Post::with(['user', 'comments', 'likes'])
            ->where('is_feeded', true)
            ->latest()
            ->get();

        return response()->json($feededPosts, 200);
    }

    public function show($id)
    {
        $post = Post::with(['comments', 'likes'])->find($id);

        if (!$post) {
            return response()->json(['message' => 'Post introuvable'], 404);
        }

        return response()->json($post, 200);
    }

    public function store(Request $request)
    {

        $this->authorize('create', Post::class);

        $validatedData = $request->validate([
            'type' => 'required|in:text,image,video,audio',
            'description' => 'required|string',
        ], [
            'type.required' => 'Le type de publication est requis.',
            'type.in' => 'Le type de publication doit être l\'un des suivants : texte, image, video, audio.',
            'description.required' => 'Le champ description est obligatoire',
            'description.string' => 'La description doit être une chaîne de caractères.',
        ]);

        $fileRules = [];
        $directory = match ($request->type) {
            'image' => 'images/postes',
            'audio' => 'audios/postes',
            'video' => 'videos/postes',
            default => null,
        };

        if ($request->type === 'image') {
            $fileRules = ['file' => 'required|file|mimes:jpeg,png,jpg|max:51200'];
        } elseif ($request->type === 'audio') {
            $fileRules = ['file' => 'required|file|mimes:mp3,wav|max:51200'];
        } elseif ($request->type === 'video') {
            $fileRules = ['file' => 'required|file|mimes:mp4,avi,wmv|max:51200'];
        }

        if ($directory && !empty($fileRules)) {

            $request->validate($fileRules, [
                'file.required' => 'Le fichier est requis pour le type de publication sélectionné.',
                'file.mimes' => 'Le fichier doit être un type valide selon la catégorie choisie (image, audio, video).',
                'file.max' => 'Le fichier ne doit pas dépasser 50MB.',
            ]);

            $extension = $request->file('file')->getClientOriginalExtension();
            $filename = uniqid() . '.' . $extension;

            $file = $request->file('file');
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path($directory), $filename);
            $fileUrl = $directory . '/' . $filename;

            $fileUrl = asset($directory . '/' . $filename);

            $validatedData['url'] = $fileUrl;
        }

        $post = Post::create($validatedData);

        return response()->json($post, 201);
    }

    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $this->authorize('update', $post);

        $validatedData = $request->validate([
            'type' => 'required|in:text,image,video,audio',
            'description' => 'nullable|string',
            'content' => 'nullable|string',
        ], [
            'type.required' => 'Le type de publication est requis.',
            'type.in' => 'Le type de publication doit être l\'un des suivants : texte, image, video, audio.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'content.string' => 'Le contenu doit être une chaîne de caractères.'
        ]);

        $directory = match ($request->type) {
            'image' => 'images',
            'audio' => 'audios',
            'video' => 'videos',
            default => null,
        };

        if ($directory) {
            $fileRules = match ($request->type) {
                'image' => ['file' => 'file|mimes:jpeg,png,jpg,gif|max:51200'],
                'audio' => ['file' => 'file|mimes:mp3,wav|max:51200'],
                'video' => ['file' => 'file|mimes:mp4,avi,wmv|max:51200'],
            };

            $fileRules['title'] = 'nullable|string|max:255';
            $request->validate($fileRules, [
                'file.mimes' => 'Le fichier doit être un type valide selon la catégorie choisie (image, audio, vidéo).',
                'file.max' => 'Le fichier ne doit pas dépasser 50MB.',
            ]);

            if ($request->hasFile('file')) {
                $extension = $request->file('file')->getClientOriginalExtension();
                $filename = uniqid() . '.' . $extension;
                $request->file('file')->move(public_path($directory), $filename);
                $fileUrl = asset($directory . '/' . $filename);

                $validatedData['url'] = $fileUrl;
            }

            $validatedData['title'] = $request->title;
        } elseif ($request->type === 'text') {
            $request->validate([
                'description' => 'required|string',
            ], [
                'description.required' => 'Le champ description est requis pour les publications de type texte.',
            ]);
        }

        $post->update($validatedData);

        return response()->json($post, 200);
    }

    public function destroy($id)
    {
        $post = Post::find($id);

        $this->authorize('delete', $post);

        if (!$post) {
            return response()->json(['message' => 'Post introuvable'], 404);
        }

        $post->delete();

        return response()->json(['message' => 'Poste supprimé avec succès'], 200);
    }
}
